<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

/**
 * Controlador del formulario de solicitudes de servicios.
 *
 * Carga catálogos para la vista, gestiona la creación de solicitudes,
 * expone endpoints JSON auxiliares y envía notificaciones vía correo.
 *
 * Se añadieron bloques try/catch y transacciones para robustecer el manejo
 * de errores sin alterar la lógica de funcionamiento ni el diseño.
 */
class FormularioController extends Controller
{
    /**
     * Vista principal del formulario y carga de catálogos.
     */
    /**
     * Renderiza la vista principal del formulario y carga catálogos.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Cargar catálogos con manejo de errores sin romper la vista
        $entidades = collect();
        $servicios = collect();
        $coordinaciones = collect();
        try {
            $entidades = DB::table('entidades_procedencia')
                ->orderBy('nombre', 'asc')
                ->get();
        } catch (\Throwable $e) {
            Log::error('[Index] Error consultando entidades', ['error' => $e->getMessage()]);
        }
        try {
            $servicios = DB::table('servicios')
                ->orderBy('nombre', 'asc')
                ->get();
        } catch (\Throwable $e) {
            Log::error('[Index] Error consultando servicios', ['error' => $e->getMessage()]);
        }
        try {
            $coordinaciones = DB::table('coordinaciones')
                ->orderBy('nombre', 'asc')
                ->get();
        } catch (\Throwable $e) {
            Log::error('[Index] Error consultando coordinaciones', ['error' => $e->getMessage()]);
        }

        return view('forms.solicitud-servicios', compact('entidades', 'servicios', 'coordinaciones'));
    }

    /**
     * Guarda la solicitud en BD.
     */
    /**
     * Guarda una solicitud en la base de datos y envía correos.
     *
     * Valida el request, normaliza datos y persiste la solicitud. Usa transacción
     * para garantizar atomicidad. En caso de error, devuelve JSON con código 500.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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

        try {
            DB::beginTransaction();

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

            DB::commit();

        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('[Store] Error de BD', ['error' => $qe->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error en la base de datos. Intenta más tarde.',
            ], 500);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[Store] Error inesperado', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado. Intenta más tarde.',
            ], 500);
        }

        // Enviar correos tras crear la solicitud: se encola (driver sync por defecto)
        try {
            \App\Jobs\SendSolicitudEmailsJob::dispatch($id);
        } catch (\Throwable $e) {
            Log::error('[Solicitud] Falló dispatch de job de correos, intentando envío directo', ['id' => $id, 'error' => $e->getMessage()]);
            try {
                $this->enviarPorSolicitud($id);
            } catch (\Throwable $ee) {
                Log::error('[Solicitud] Falló envío directo de correos', ['id' => $id, 'error' => $ee->getMessage()]);
            }
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
    /**
     * Devuelve el catálogo de servicios en formato JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function servicios()
    {
        try {
            $servicios = DB::table('servicios')
                ->orderBy('nombre', 'asc')
                ->get(['id', 'nombre', 'coordinacion_predeterminada_id']);
            return response()->json($servicios);
        } catch (\Throwable $e) {
            Log::error('[Servicios] Error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error interno al consultar servicios'], 500);
        }
    }

    /**
     * Devuelve la coordinación predeterminada de un servicio.
     */
    /**
     * Devuelve la coordinación predeterminada de un servicio dado.
     *
     * @param int $servicioId
     * @return \Illuminate\Http\JsonResponse
     */
    public function coordinacion(int $servicioId)
    {
        try {
            $servicio = DB::table('servicios')->where('id', $servicioId)->first();
            if (!$servicio) {
                return response()->json(['coordinacion_id' => null], 404);
            }
            return response()->json(['coordinacion_id' => $servicio->coordinacion_predeterminada_id]);
        } catch (\Throwable $e) {
            Log::error('[Coordinacion] Error', ['servicioId' => $servicioId, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Error interno al consultar coordinación'], 500);
        }
    }

    /**
     * Devuelve coordinaciones en formato JSON.
     */
    /**
     * Devuelve el catálogo de coordinaciones en formato JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function coordinaciones()
    {
        try {
            $coordinaciones = DB::table('coordinaciones')
                ->orderBy('nombre', 'asc')
                ->get(['id', 'nombre']);
            return response()->json($coordinaciones);
        } catch (\Throwable $e) {
            Log::error('[Coordinaciones] Error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error interno al consultar coordinaciones'], 500);
        }
    }

    /**
     * Devuelve entidades de procedencia en formato JSON.
     */
    /**
     * Devuelve el catálogo de entidades de procedencia en formato JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function entidades()
    {
        try {
            $entidades = DB::table('entidades_procedencia')
                ->orderBy('nombre', 'asc')
                ->get(['id', 'nombre']);
            return response()->json($entidades);
        } catch (\Throwable $e) {
            Log::error('[Entidades] Error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error interno al consultar entidades'], 500);
        }
    }

    /**
     * Enviar correos por solicitud (mantiene la lógica existente).
     */
    /**
     * Envía correos asociados a una solicitud ya creada.
     *
     * Realiza consultas auxiliares para completar contexto y construye el
     * contenido de los correos. Los errores se registran y no impactan
     * la respuesta de creación.
     *
     * @param int $solicitudId
     * @return void
     */
    public function enviarPorSolicitud(int $solicitudId): void
    {
        try {
            $sol = DB::table('solicitudes_servicios')->where('id', $solicitudId)->first();
        } catch (\Throwable $e) {
            Log::error('[EnviarPorSolicitud] Error consultando solicitud', ['id' => $solicitudId, 'error' => $e->getMessage()]);
            return;
        }
        if (!$sol) return;

        $nombreSolicitante = trim(implode(' ', array_filter([
            $sol->nombres,
            $sol->apellido_paterno,
            $sol->apellido_materno,
        ])));

        $servicioNombre = $sol->servicio_otro;
        if ($servicioNombre === null) {
            try {
                $servicioNombre = DB::table('servicios')
                    ->where('id', $sol->servicio_id)
                    ->value('nombre');
            } catch (\Throwable $e) {
                Log::error('[EnviarPorSolicitud] Error consultando servicio', ['servicio_id' => $sol->servicio_id, 'error' => $e->getMessage()]);
            }
        }

        try {
            $coordinacion = DB::table('coordinaciones')->where('id', $sol->coordinacion_id)->first();
        } catch (\Throwable $e) {
            Log::error('[EnviarPorSolicitud] Error consultando coordinación', ['coordinacion_id' => $sol->coordinacion_id, 'error' => $e->getMessage()]);
            $coordinacion = null;
        }
        
        $esOtro = ($sol->servicio_id === null && $sol->servicio_otro !== null);
        $entidadNombre = null;
        if (!is_null($sol->entidad_procedencia_id)) {
            try {
                $entidadNombre = DB::table('entidades_procedencia')->where('id', $sol->entidad_procedencia_id)->value('nombre');
            } catch (\Throwable $e) {
                Log::error('[EnviarPorSolicitud] Error consultando entidad', ['entidad_id' => $sol->entidad_procedencia_id, 'error' => $e->getMessage()]);
                $entidadNombre = null;
            }
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
                'cuerpo' => "Estimado(a) @{{solicitante.nombre_completo}} ,\nHemos recibido tu solicitud para el servicio de \"@{{servicio.nombre}}\" .\nTu requerimiento será revisado por el equipo del Instituto Mexicano del Transporte , y nos pondremos en contacto contigo a la brevedad a través de este mismo medio para brindarte la atención correspondiente.\nAgradecemos tu confianza y el interés en nuestros servicios.",
                'despedida' => 'Saludos cordiales, Instituto Mexicano del Transporte',
            ];

            $defaults['representante'] = [
                'titulo' => '¡Notificación de nueva solicitud de servicio!',
                'cuerpo' => "Estimado(a) representante,\n\nSe informa que la @{{coordinacion.nombre}} ha recibido una nueva solicitud correspondiente al servicio @{{servicio.nombre}}, registrada con el número de folio @{{solicitud.id}}.\n\nDado que este servicio no se encuentra dentro del catálogo oficial, se solicita su revisión y atención a la brevedad.\n\nA continuación, se presenta la información del solicitante:\n\nNombre: @{{solicitante.nombre}}\n\nApellido paterno: @{{solicitante.apellido_paterno}}\n\nApellido materno: @{{solicitante.apellido_materno}}\n\nEntidad de procedencia: @{{solicitante.entidad}}\n\nTeléfono: @{{solicitante.telefono}}\n\nCorreo electrónico: @{{solicitante.correo}}\n\nMotivo de la solicitud: @{{solicitud.motivo_solicitud}}\n\nSe agradece su apoyo para brindar seguimiento oportuno conforme a los procedimientos establecidos.",
                'despedida' => 'Atentamente, División de Telemática',
            ];
        }

        $ctx = [
            '@{{solicitud.id}}' => (string)$sol->id,
            '@{{ solicitud.id }}' => (string)$sol->id,

            // Datos del solicitante
            '@{{solicitante.nombre_completo}}' => $nombreSolicitante,
            '@{{ solicitante.nombre_completo }}' => $nombreSolicitante,
            '@{{solicitante.nombre}}' => ($sol->nombres ?? ''),
            '@{{ solicitante.nombre }}' => ($sol->nombres ?? ''),
            '@{{solicitante.nombres}}' => ($sol->nombres ?? ''),
            '@{{ solicitante.nombres }}' => ($sol->nombres ?? ''),
            '@{{solicitante.apellido_paterno}}' => ($sol->apellido_paterno ?? ''),
            '@{{ solicitante.apellido_paterno }}' => ($sol->apellido_paterno ?? ''),
            '@{{solicitante.apellido_materno}}' => ($sol->apellido_materno ?? ''),
            '@{{ solicitante.apellido_materno }}' => ($sol->apellido_materno ?? ''),
            '@{{solicitante.telefono}}' => ($sol->telefono ?? ''),
            '@{{ solicitante.telefono }}' => ($sol->telefono ?? ''),
            '@{{solicitante.correo}}' => $sol->correo_electronico,
            '@{{ solicitante.correo }}' => $sol->correo_electronico,
            '@{{solicitante.entidad}}' => ($entidadNombre ?? ''),
            '@{{ solicitante.entidad }}' => ($entidadNombre ?? ''),

            // Datos de la solicitud
            '@{{solicitud.motivo_solicitud}}' => ($sol->motivo_solicitud ?? ''),
            '@{{ solicitud.motivo_solicitud }}' => ($sol->motivo_solicitud ?? ''),
            '@{{solicitud.fecha_solicitud}}' => (string)($sol->fecha_solicitud ?? ''),
            '@{{ solicitud.fecha_solicitud }}' => (string)($sol->fecha_solicitud ?? ''),

            // Datos del servicio y coordinación
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
            // Si no hay coordinación asignada (NULL), seleccionar una solo para notificar al representante
            $coordinacionNotif = $coordinacion ?? (function () {
                try {
                    return DB::table('coordinaciones')->orderBy('id', 'asc')->first();
                } catch (\Throwable $e) {
                    Log::error('[EnviarPorSolicitud] Error consultando coordinación para notificación', ['error' => $e->getMessage()]);
                    return null;
                }
            })();
            if ($coordinacionNotif) {
                $send('representante', $coordinacionNotif->correo_representante ?? '');
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

    /**
     * Vista de listado de solicitudes con filtros (AJAX, sin recarga).
     */
    /**
     * Renderiza la vista de listado de solicitudes con filtros.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function solicitudesIndex(Request $request)
    {
        try {
            $servicios = DB::table('servicios')->orderBy('nombre', 'asc')->get(['id', 'nombre']);
            $coordinaciones = DB::table('coordinaciones')->orderBy('nombre', 'asc')->get(['id', 'nombre']);
            $estatus = DB::table('solicitudes_servicios')->select('estatus')->distinct()->pluck('estatus');
            return view('solicitudes.index', compact('servicios', 'coordinaciones', 'estatus'));
        } catch (\Throwable $e) {
            Log::error('[SolicitudesIndex] Error', ['error' => $e->getMessage()]);
            // Mantener la vista sin romper diseño
            $servicios = collect();
            $coordinaciones = collect();
            $estatus = collect();
            return view('solicitudes.index', compact('servicios', 'coordinaciones', 'estatus'));
        }
    }

    /**
     * Endpoint que devuelve el HTML del listado filtrado con paginación.
     */
    /**
     * Devuelve el HTML del listado filtrado con paginación, vía AJAX.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function solicitudesData(Request $request)
    {
        try {
            $q = trim((string)$request->query('q', ''));
            $status = $request->query('estatus');
            $servicioId = $request->query('servicio_id');
            $coordinacionId = $request->query('coordinacion_id');
            $perPage = (int)$request->query('per_page', 10);

            $query = DB::table('solicitudes_servicios as s')
                ->leftJoin('servicios as sv', 'sv.id', '=', 's.servicio_id')
                ->leftJoin('coordinaciones as c', 'c.id', '=', 's.coordinacion_id')
                ->select(
                    's.id', 's.nombres', 's.apellido_paterno', 's.apellido_materno', 's.correo_electronico',
                    's.servicio_id', 's.servicio_otro', 's.estatus', 's.coordinacion_id',
                    DB::raw('COALESCE(s.servicio_otro, sv.nombre) as servicio_nombre'),
                    'c.nombre as coordinacion_nombre'
                )
                ->orderByDesc('s.id');

            if ($q !== '') {
                $query->where(function ($w) use ($q) {
                    $w->where('s.nombres', 'like', "%$q%")
                      ->orWhere('s.apellido_paterno', 'like', "%$q%")
                      ->orWhere('s.apellido_materno', 'like', "%$q%")
                      ->orWhere('s.correo_electronico', 'like', "%$q%")
                      ->orWhere('sv.nombre', 'like', "%$q%")
                      ->orWhere('s.servicio_otro', 'like', "%$q%")
                      ->orWhere('c.nombre', 'like', "%$q%");
                });
            }
            if (!empty($status)) {
                $query->where('s.estatus', $status);
            }
            if (!empty($servicioId) && is_numeric($servicioId)) {
                $query->where('s.servicio_id', (int)$servicioId);
            }
            if (!empty($coordinacionId) && is_numeric($coordinacionId)) {
                $query->where('s.coordinacion_id', (int)$coordinacionId);
            }

            $solicitudes = $query->paginate($perPage)->appends($request->query());

            $html = view('solicitudes._table', compact('solicitudes'))->render();
            return response($html);
        } catch (\Throwable $e) {
            Log::error('[SolicitudesData] Error', ['error' => $e->getMessage()]);
            return response('Ocurrió un error al consultar solicitudes.', 500);
        }
    }

}