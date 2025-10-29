<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitudesServiciosController extends Controller
{
    /**
     * Muestra la vista principal y carga los catálogos desde la BD.
     */
    public function index(Request $request)
    {
        $entidades = DB::table('entidades_procedencia')
            ->where('activo', 1)
            ->orderBy('nombre', 'asc')
            ->get();

        $servicios = DB::table('servicios')
            ->where('activo', 1)
            ->orderBy('nombre', 'asc')
            ->get();

        $coordinaciones = DB::table('coordinaciones')
            ->where('activo', 1)
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
            'entidad_otra' => ['nullable', 'string', 'max:200'],
            'servicio' => ['required'], // puede ser id o 'otro'
            'servicio_otro' => ['nullable', 'string', 'max:200'],
            'coordinacion' => ['required', 'integer', 'exists:coordinaciones,id'],
            'motivo_solicitud' => ['required', 'string'],
        ]);

        // Normalizar valores "otra"/"otro"
        $entidadRaw = $validated['entidad_procedencia'];
        $servicioRaw = $validated['servicio'];

        $entidadId = is_numeric($entidadRaw) ? (int)$entidadRaw : null;
        $entidadOtra = ($entidadRaw === 'otra') ? ($validated['entidad_otra'] ?? null) : null;

        $servicioId = is_numeric($servicioRaw) ? (int)$servicioRaw : null;
        $servicioOtro = ($servicioRaw === 'otro') ? ($validated['servicio_otro'] ?? null) : null;

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
            'nombres' => $validated['nombres'],
            'apellido_paterno' => $validated['apellido_paterno'],
            'apellido_materno' => $validated['apellido_materno'] ?? null,
            'telefono' => $validated['telefono'],
            'correo_electronico' => $validated['correo_electronico'],
            'entidad_procedencia_id' => $entidadId,
            'entidad_otra' => $entidadOtra,
            'servicio_id' => $servicioId,
            'servicio_otro' => $servicioOtro,
            'coordinacion_id' => (int)$validated['coordinacion'],
            'motivo_solicitud' => $validated['motivo_solicitud'],
            // 'estatus' => 'pendiente', // usa default
        ];

        $id = DB::table('solicitudes_servicios')->insertGetId($insertData);

        return response()->json([
            'success' => true,
            'message' => 'Su solicitud ha sido recibida y será atendida a la brevedad.',
            'id' => $id,
        ]);
    }
}