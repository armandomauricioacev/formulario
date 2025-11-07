<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class FormularioController extends Controller
{
    /**
     * Vista principal del formulario y carga de catálogos.
     */
    public function index(Request $request)
    {
        $entidades = DB::table('entidades_procedencia')
            ->orderBy('nombre', 'asc')
            ->get();

        $servicios = DB::table('servicios')
            ->orderBy('nombre', 'asc')
            ->get();

        $coordinaciones = DB::table('coordinaciones')
            ->orderBy('nombre', 'asc')
            ->get();

        return view('forms.solicitud-servicios', compact('entidades', 'servicios', 'coordinaciones'));
    }

    /**
     * Guarda la solicitud en BD.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombres' => ['required', 'string', 'max:60'],
            'apellido_paterno' => ['required', 'string', 'max:60'],
            'apellido_materno' => ['nullable', 'string', 'max:60'],
            'telefono' => ['required', 'regex:/^\d{10}$/'],
            'correo_electronico' => ['required', 'string', 'email', 'max:100'],
            'entidad_procedencia' => ['required'],
            'entidad_otra' => ['nullable', 'required_if:entidad_procedencia,otra', 'string', 'max:200'],
            'servicio' => ['required'],
            'servicio_otro' => ['nullable', 'required_if:servicio,otro', 'string', 'max:200'],
            'coordinacion' => [
                'exclude_if:servicio,otro',
                'nullable',
                'integer',
                'exists:coordinaciones,id'
            ],
            'motivo_solicitud' => ['required', 'string'],
        ]);

        $entidadRaw = $validated['entidad_procedencia'];
        $servicioRaw = $validated['servicio'];

        $entidadId = is_numeric($entidadRaw) ? (int)$entidadRaw : null;
        $entidadOtra = ($entidadRaw === 'otra') ? ($validated['entidad_otra'] ?? null) : null;
        if ($entidadOtra !== null) {
            $entidadOtra = mb_strtoupper($entidadOtra, 'UTF-8');
        }

        $servicioId = is_numeric($servicioRaw) ? (int)$servicioRaw : null;
        $servicioOtro = ($servicioRaw === 'otro') ? ($validated['servicio_otro'] ?? null) : null;
        if ($servicioOtro !== null) {
            $servicioOtro = mb_strtoupper($servicioOtro, 'UTF-8');
        }

        $coordinacionId = isset($validated['coordinacion']) && $validated['coordinacion'] !== ''
            ? (int)$validated['coordinacion']
            : null;

        if ($coordinacionId === null && $servicioId !== null) {
            $coordinacionId = DB::table('servicios')
                ->where('id', $servicioId)
                ->value('coordinacion_predeterminada_id');
        }

        if ($coordinacionId === null && $servicioId === null && $servicioOtro !== null) {
            $coordinacionId = DB::table('coordinaciones')
                ->orderBy('id', 'asc')
                ->value('id');

            if ($coordinacionId === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay coordinaciones disponibles para asignar por defecto.',
                ], 422);
            }
        }

        if ($entidadId === null && $entidadOtra === null) {
            return response()->json([
                'success' => false,
                'message' => 'Debes seleccionar una entidad o especificar "Otra".',
            ], 422);
        }
        if ($servicioId === null && $servicioOtro === null) {
            return response()->json([
                'success' => false,
                'message' => 'Debes seleccionar un servicio o especificar "Otro".',
            ], 422);
        }

        // Persistir "Otro servicio" en la tabla servicios cuando aplique (solo si existe la columna)
        if ($servicioId === null && $servicioOtro !== null && Schema::hasColumn('servicios', 'otro_servicio')) {
            $placeholder = DB::table('servicios')->where('nombre', 'Otro')->first();
            if ($placeholder) {
                DB::table('servicios')
                    ->where('id', $placeholder->id)
                    ->update([
                        'otro_servicio' => $servicioOtro,
                        'coordinacion_predeterminada_id' => $coordinacionId,
                    ]);
            } else {
                DB::table('servicios')->insert([
                    'nombre' => 'Otro',
                    'coordinacion_predeterminada_id' => $coordinacionId,
                    'otro_servicio' => $servicioOtro,
                ]);
            }
        }

        $id = DB::table('solicitudes_servicios')->insertGetId([
            'nombres' => mb_strtoupper($validated['nombres'], 'UTF-8'),
            'apellido_paterno' => mb_strtoupper($validated['apellido_paterno'], 'UTF-8'),
            'apellido_materno' => isset($validated['apellido_materno']) && $validated['apellido_materno'] !== null ? mb_strtoupper($validated['apellido_materno'], 'UTF-8') : null,
            'telefono' => preg_replace('/\D+/', '', $validated['telefono']),
            'correo_electronico' => $validated['correo_electronico'],
            'entidad_procedencia_id' => $entidadId,
            'entidad_otra' => $entidadOtra,
            'servicio_id' => $servicioId,
            'servicio_otro' => $servicioOtro,
            'coordinacion_id' => $coordinacionId !== null ? (int)$coordinacionId : null,
            'motivo_solicitud' => $validated['motivo_solicitud'],
            'estatus' => 'en_revision',
        ]);

        // Enviar correos tras crear la solicitud (no bloquea la respuesta)
        try {
            $this->enviarPorSolicitud($id);
        } catch (\Throwable $e) {
            Log::error('[Solicitud] Falló envío de correos', ['id' => $id, 'error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Gracias por completar el formulario. El Instituto Mexicano del Transporte revisará su solicitud y le contactará a la brevedad al correo proporcionado.',
            'id' => $id,
        ]);
    }

    /**
     * Devuelve servicios en formato JSON.
     */
    public function servicios()
    {
        $servicios = DB::table('servicios')
            ->orderBy('nombre', 'asc')
            ->get(['id', 'nombre', 'coordinacion_predeterminada_id']);

        return response()->json($servicios);
    }

    /**
     * Devuelve la coordinación predeterminada de un servicio.
     */
    public function coordinacion(int $servicioId)
    {
        $servicio = DB::table('servicios')->where('id', $servicioId)->first();
        if (!$servicio) {
            return response()->json(['coordinacion_id' => null], 404);
        }
        return response()->json(['coordinacion_id' => $servicio->coordinacion_predeterminada_id]);
    }

    /**
     * Devuelve coordinaciones en formato JSON.
     */
    public function coordinaciones()
    {
        $coordinaciones = DB::table('coordinaciones')
            ->orderBy('nombre', 'asc')
            ->get(['id', 'nombre']);

        return response()->json($coordinaciones);
    }

    /**
     * Devuelve entidades de procedencia en formato JSON.
     */
    public function entidades()
    {
        $entidades = DB::table('entidades_procedencia')
            ->orderBy('nombre', 'asc')
            ->get(['id', 'nombre']);

        return response()->json($entidades);
    }

    /**
     * Enviar correos por solicitud (mantiene la lógica existente).
     */
    public function enviarPorSolicitud(int $solicitudId): void
    {
        $sol = DB::table('solicitudes_servicios')->where('id', $solicitudId)->first();
        if (!$sol) return;

        $nombreSolicitante = trim(implode(' ', array_filter([
            $sol->nombres,
            $sol->apellido_paterno,
            $sol->apellido_materno,
        ])));

        $servicioNombre = $sol->servicio_otro ?? DB::table('servicios')
            ->where('id', $sol->servicio_id)
            ->value('nombre');

        $coordinacion = DB::table('coordinaciones')->where('id', $sol->coordinacion_id)->first();

        $esOtro = ($sol->servicio_id === null && $sol->servicio_otro !== null);
        $entidadNombre = null;
        if (!is_null($sol->entidad_procedencia_id)) {
            $entidadNombre = DB::table('entidades_procedencia')->where('id', $sol->entidad_procedencia_id)->value('nombre');
        } else {
            $entidadNombre = $sol->entidad_otra ?? null;
        }

        $regs = collect();
        if (Schema::hasTable('correos')) {
            try {
                $regs = DB::table('correos')->get()->keyBy('tipo');
            } catch (\Throwable $e) {
                $regs = collect();
            }
        }

        $defaults = [
            'solicitante' => [
                'titulo' => '¡Hemos recibido tu solicitud!',
                'cuerpo' => "Hola @{{solicitante.nombre_completo}},\nNos hemos percatado de tu solicitud para el servicio de @{{servicio.nombre}}.\nA la brevedad por medio de este correo, nos comunicaremos contigo.",
                'despedida' => 'Saludos cordiales, Instituto Mexicano del Transporte',
            ],
            'coordinador' => [
                'titulo' => '¡Nueva solicitud de servicios requerido!',
                'cuerpo' => "Hola,\nHas recibido una nueva notificación sobre una solicitud para el servicio de @{{servicio.nombre}}.",
                'despedida' => 'Atentamente, División de Telemática',
            ],
            'asistente' => [
                'titulo' => '¡Apoyo requerido!',
                'cuerpo' => "Hola,\nHas recibido una nueva notificación sobre una solicitud para el servicio de @{{servicio.nombre}}.",
                'despedida' => 'Atentamente, División de Telemática',
            ],
            'representante' => [
                'titulo' => '¡Notificación de nueva solicitud!',
                'cuerpo' => "Hola,\nAviso de que tal coordinación ha recibido una solicitud del servicio de @{{servicio.nombre}}.\nEn espera de ser atentida.",
                'despedida' => 'Atentamente, División de Telemática',
            ],
        ];

        if ($esOtro) {
            $defaults['solicitante'] = [
                'titulo' => '¡Hemos recibido tu solicitud!',
                'cuerpo' => "Hola @{{solicitante.nombre_completo}},\nHemos recibido tu solicitud para el servicio \"@{{servicio.nombre}}\".\nLe daremos seguimiento desde el Instituto Mexicano del Transporte y nos pondremos en contacto contigo a la brevedad por este medio.",
                'despedida' => 'Saludos cordiales, Instituto Mexicano del Transporte',
            ];

            $cuerpoRep =
                "Se ha seleccionado el servicio \"" . ($sol->servicio_otro ?? '') . "\" que no está en nuestro catálogo. Favor de revisarlo y atenderlo a la brevedad.\n\n" .
                "Información del solicitante:\n" .
                "Nombre: " . ($sol->nombres ?? '') . "\n" .
                "Apellido Paterno: " . ($sol->apellido_paterno ?? '') . "\n" .
                "Apellido Materno: " . ($sol->apellido_materno ?? '') . "\n" .
                "Entidad de procedencia: " . ($entidadNombre ?? '') . "\n" .
                "Teléfono: " . ($sol->telefono ?? '') . "\n" .
                "Correo: " . ($sol->correo_electronico ?? '') . "\n" .
                "Motivo: " . ($sol->motivo_solicitud ?? '');

            $defaults['representante'] = [
                'titulo' => '¡Notificación de nueva solicitud de servicio!',
                'cuerpo' => $cuerpoRep,
                'despedida' => 'Atentamente, División de Telemática',
            ];
        }

        $ctx = [
            '@{{solicitud.id}}' => (string)$sol->id,
            '@{{ solicitud.id }}' => (string)$sol->id,

            '@{{solicitante.nombre_completo}}' => $nombreSolicitante,
            '@{{ solicitante.nombre_completo }}' => $nombreSolicitante,

            '@{{solicitante.correo}}' => $sol->correo_electronico,
            '@{{ solicitante.correo }}' => $sol->correo_electronico,

            '@{{servicio.nombre}}' => $servicioNombre,
            '@{{ servicio.nombre }}' => $servicioNombre,

            '@{{coordinacion.nombre}}' => $coordinacion->nombre ?? '',
            '@{{ coordinacion.nombre }}' => ($coordinacion->nombre ?? ''),

            '@{{coordinacion.coordinador}}' => $coordinacion->coordinador ?? '',
            '@{{ coordinacion.coordinador }}' => ($coordinacion->coordinador ?? ''),

            '@{{coordinacion.asistente}}' => $coordinacion->asistente ?? '',
            '@{{ coordinacion.asistente }}' => ($coordinacion->asistente ?? ''),

            '@{{coordinacion.representante}}' => $coordinacion->representante ?? '',
            '@{{ coordinacion.representante }}' => ($coordinacion->representante ?? ''),
        ];

        $ignorarDbTipos = $esOtro ? ['solicitante', 'representante'] : [];

        $send = function (string $tipo, string $to) use ($regs, $defaults, $ctx, $ignorarDbTipos) {
            if (!$to) return;
            $tpl = in_array($tipo, $ignorarDbTipos, true) ? null : ($regs[$tipo] ?? null);
            $titulo = ($tpl->titulo ?? $defaults[$tipo]['titulo']);
            $cuerpo = ($tpl->cuerpo ?? $defaults[$tipo]['cuerpo']);
            $despedida = ($tpl->despedida ?? $defaults[$tipo]['despedida']);

            foreach ($ctx as $k => $v) {
                $titulo = str_replace($k, $v, $titulo);
                $cuerpo = str_replace($k, $v, $cuerpo);
                $despedida = str_replace($k, $v, $despedida);
            }

            $lineas = array_map('trim', preg_split("/\r?\n/", $cuerpo));

            try {
                Log::info('[Correo] Enviando', ['tipo' => $tipo, 'to' => $to, 'subject' => $titulo, 'mailer' => config('mail.default')]);
                Mail::send('emails.plantilla', [
                    'titulo' => $titulo,
                    'lineas' => $lineas,
                    'despedida' => $despedida,
                ], function ($message) use ($to, $titulo) {
                    $message->to($to)->subject($titulo);
                });
                Log::info('[Correo] Enviado OK', ['tipo' => $tipo, 'to' => $to]);
            } catch (\Throwable $e) {
                Log::error('[Correo] Error al enviar', ['tipo' => $tipo, 'to' => $to, 'error' => $e->getMessage()]);
            }
        };

        if ($esOtro) {
            $send('solicitante', $sol->correo_electronico);
            if ($coordinacion) {
                $send('representante', $coordinacion->correo_representante ?? '');
            }
            return;
        } else {
            $send('solicitante', $sol->correo_electronico);
            if ($coordinacion) {
                $send('coordinador', $coordinacion->correo_coordinador ?? '');
                $send('asistente', $coordinacion->correo_asistente ?? '');
                $send('representante', $coordinacion->correo_representante ?? '');
            }
        }
    }
}