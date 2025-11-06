<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Correo;

class CorreoController extends Controller
{
    // Nota: index/update se removieron de las rutas porque prefieres editar desde MySQL.

    /**
     * Enviar los 4 correos al crear una solicitud.
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

        // Detectar caso especial: servicio "otro"
        $esOtro = ($sol->servicio_id === null && $sol->servicio_otro !== null);
        $entidadNombre = null;
        if (!is_null($sol->entidad_procedencia_id)) {
            $entidadNombre = DB::table('entidades_procedencia')->where('id', $sol->entidad_procedencia_id)->value('nombre');
        } else {
            $entidadNombre = $sol->entidad_otra ?? null;
        }

        // Plantillas almacenadas en BD si la tabla existe; si no, usar defaults
        $regs = collect();
        if (Schema::hasTable('correos')) {
            try {
                $regs = DB::table('correos')->get()->keyBy('tipo');
            } catch (\Throwable $e) {
                $regs = collect();
            }
        }

        // Defaults
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

        // Si el servicio es "otro", sobrescribir únicamente solicitante y representante
        if ($esOtro) {
            $defaults['solicitante'] = [
                'titulo' => '¡Hemos recibido tu solicitud!',
                'cuerpo' => "Le daremos seguimiento a tu servicio que requieres del Instituto Mexicano del Transporte. Espera hasta que nosotros nos comuniquemos contigo por este medio.",
                'despedida' => 'Saludos cordiales, Instituto Mexicano del Transporte',
            ];

            $cuerpoRep =
                "Hola, " . (($coordinacion->representante ?? '') ?: 'representante') . ", espero te encuentres muy bien.\n" .
                "Se ha seleccionado el servicio \"" . ($sol->servicio_otro ?? '') . "\" que no está en nuestro catálogo. Favor de revisarlo y atenderlo a la brevedad.\n\n" .
                "Información del solicitante:\n" .
                "Nombre: " . ($sol->nombres ?? '') . "\n" .
                "Apellido Paterno: " . ($sol->apellido_paterno ?? '') . "\n" .
                "Apellido Materno: " . ($sol->apellido_materno ?? '') . "\n" .
                "Entidad: " . ($entidadNombre ?? '') . "\n" .
                "Teléfono: " . ($sol->telefono ?? '') . "\n" .
                "Correo: " . ($sol->correo_electronico ?? '') . "\n" .
                "Motivo: " . ($sol->motivo_solicitud ?? '');

            $defaults['representante'] = [
                'titulo' => '¡Notificación de nueva solicitud de servicio!',
                'cuerpo' => $cuerpoRep,
                'despedida' => 'Atentamente, División de Telemática',
            ];
        }

        // Mapeo de placeholders: soporta variantes con y sin espacios para coincidir
        // plantillas existentes: @{{foo.bar}} y @{{ foo.bar }}
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

        // Cuando el servicio es "otro", debemos ignorar cualquier plantilla guardada en BD
        $ignorarDbTipos = $esOtro ? ['solicitante', 'representante'] : [];

        $send = function (string $tipo, string $to) use ($regs, $defaults, $ctx, $ignorarDbTipos) {
            if (!$to) return;
            $tpl = in_array($tipo, $ignorarDbTipos, true) ? null : ($regs[$tipo] ?? null);
            $titulo = ($tpl->titulo ?? $defaults[$tipo]['titulo']);
            $cuerpo = ($tpl->cuerpo ?? $defaults[$tipo]['cuerpo']);
            $despedida = ($tpl->despedida ?? $defaults[$tipo]['despedida']);

            // Reemplazar marcadores
            foreach ($ctx as $k => $v) {
                $titulo = str_replace($k, $v, $titulo);
                $cuerpo = str_replace($k, $v, $cuerpo);
                $despedida = str_replace($k, $v, $despedida);
            }

            $lineas = array_map('trim', preg_split("/\r?\n/", $cuerpo));

            $html = view('emails.plantilla', [
                'titulo' => $titulo,
                'lineas' => $lineas,
                'despedida' => $despedida,
            ])->render();

            try {
                Log::info('[Correo] Enviando', ['tipo' => $tipo, 'to' => $to, 'subject' => $titulo, 'mailer' => config('mail.default')]);
                Mail::html($html, function ($message) use ($to, $titulo) {
                    $message->to($to)->subject($titulo);
                });
                Log::info('[Correo] Enviado OK', ['tipo' => $tipo, 'to' => $to]);
            } catch (\Throwable $e) {
                Log::error('[Correo] Error al enviar', ['tipo' => $tipo, 'to' => $to, 'error' => $e->getMessage()]);
            }
        };

        // Enviar correos según el caso
        if ($esOtro) {
            // Solo solicitante y representante
            $send('solicitante', $sol->correo_electronico);
            if ($coordinacion) {
                $send('representante', $coordinacion->correo_representante ?? '');
            }
            return; // no enviar a coordinador/asistente
        } else {
            // Flujo normal: solicitante + coordinación completa
            $send('solicitante', $sol->correo_electronico);
            if ($coordinacion) {
                $send('coordinador', $coordinacion->correo_coordinador ?? '');
                $send('asistente', $coordinacion->correo_asistente ?? '');
                $send('representante', $coordinacion->correo_representante ?? '');
            }
        }
    }
}