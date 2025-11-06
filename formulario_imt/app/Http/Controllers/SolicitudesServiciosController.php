<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SolicitudesServiciosController extends Controller
{
    /**
     * Muestra la vista principal y carga los catálogos desde la BD.
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
     * Guarda la solicitud (temporal: respuesta JSON de éxito).
     */
    public function store(Request $request)
    {
        // Validación de campos
        $validated = $request->validate([
            'nombres' => ['required', 'string', 'max:60'],
            'apellido_paterno' => ['required', 'string', 'max:60'],
            'apellido_materno' => ['nullable', 'string', 'max:60'],
            'telefono' => ['required', 'regex:/^\d{10}$/'],
            'correo_electronico' => ['required', 'string', 'email', 'max:100'],
            'entidad_procedencia' => ['required'], // puede ser id o 'otra'
            'entidad_otra' => ['nullable', 'required_if:entidad_procedencia,otra', 'string', 'max:200'],
            'servicio' => ['required'], // puede ser id o 'otro'
            'servicio_otro' => ['nullable', 'required_if:servicio,otro', 'string', 'max:200'],
            // Coordinación: permitir null y excluir cuando servicio es "otro"; si falta, se resuelve por backend
            'coordinacion' => [
                'exclude_if:servicio,otro',
                'nullable',
                'integer',
                'exists:coordinaciones,id'
            ],
            'motivo_solicitud' => ['required', 'string'],
        ]);

        // Normalizar valores "otra"/"otro"
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

        // Determinar coordinación final (hidden o fallback por servicio)
        $coordinacionId = isset($validated['coordinacion']) && $validated['coordinacion'] !== ''
            ? (int)$validated['coordinacion']
            : null;

        if ($coordinacionId === null && $servicioId !== null) {
            $coordinacionId = DB::table('servicios')
                ->where('id', $servicioId)
                ->value('coordinacion_predeterminada_id');
        }

        // Si el servicio es "otro" y no se envió coordinación, asignar una por defecto
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

        // Reglas condicionales
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

        // Insertar en la tabla solicitudes_servicios
        $insertData = [
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
        ];

        $id = DB::table('solicitudes_servicios')->insertGetId($insertData);

        // Enviar correos (solicitante, coordinador, asistente, representante)
        try {
            app(CorreoController::class)->enviarPorSolicitud($id);
        } catch (\Throwable $e) {
            Log::error('Error enviando correos de solicitud: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Gracias por completar el formulario. El Instituto Mexicano del Transporte revisará su solicitud y le contactará a la brevedad al correo proporcionado.',
            'id' => $id,
        ]);
    }
}