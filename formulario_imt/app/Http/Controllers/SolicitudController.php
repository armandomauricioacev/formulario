<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SolicitudController extends Controller
{
    /**
     * Muestra el formulario de solicitud
     */
    public function index()
    {
        // Obtener catálogos de la base de datos
        $entidades = DB::table('entidades_procedencia')
            ->where('activo', 1)
            ->orderBy('nombre')
            ->get();
        
        $servicios = DB::table('servicios')
            ->where('activo', 1)
            ->orderBy('nombre')
            ->get();
        
        $coordinaciones = DB::table('coordinaciones')
            ->where('activo', 1)
            ->orderBy('nombre')
            ->get();
        
        return view('solicitud.index', compact('entidades', 'servicios', 'coordinaciones'));
    }
    
    /**
     * Obtiene la coordinación predeterminada de un servicio (AJAX)
     */
    public function obtenerCoordinacionServicio($servicioId)
    {
        $servicio = DB::table('servicios')
            ->where('id', $servicioId)
            ->first();
        
        if ($servicio) {
            return response()->json([
                'success' => true,
                'coordinacion_id' => $servicio->coordinacion_predeterminada_id
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Servicio no encontrado'
        ], 404);
    }
    
    /**
     * Guarda la solicitud en la base de datos
     */
    public function store(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:60',
            'apellido_paterno' => 'required|string|max:60',
            'apellido_materno' => 'nullable|string|max:60',
            'telefono' => 'required|digits:10',
            'correo_electronico' => 'required|email|max:100',
            'entidad_procedencia' => 'required',
            'entidad_otra' => 'nullable|required_if:entidad_procedencia,otra|string|max:200',
            'servicio' => 'required',
            'servicio_otro' => 'nullable|required_if:servicio,otro|string|max:200',
            'coordinacion' => 'required|integer|exists:coordinaciones,id',
            'motivo_solicitud' => 'required|string',
        ], [
            'nombres.required' => 'El campo nombre(s) es obligatorio',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio',
            'telefono.required' => 'El teléfono es obligatorio',
            'telefono.digits' => 'El teléfono debe tener 10 dígitos',
            'correo_electronico.required' => 'El correo electrónico es obligatorio',
            'correo_electronico.email' => 'El correo electrónico no es válido',
            'entidad_procedencia.required' => 'Debe seleccionar una entidad de procedencia',
            'entidad_otra.required_if' => 'Debe especificar la entidad cuando selecciona "Otra"',
            'servicio.required' => 'Debe seleccionar un servicio',
            'servicio_otro.required_if' => 'Debe especificar el servicio cuando selecciona "Otro"',
            'coordinacion.required' => 'Debe seleccionar una coordinación',
            'motivo_solicitud.required' => 'El motivo de la solicitud es obligatorio',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Preparar datos para inserción
            $entidadId = null;
            $entidadOtra = null;
            
            if ($request->entidad_procedencia === 'otra') {
                $entidadOtra = strtoupper($request->entidad_otra);
            } else {
                $entidadId = (int) $request->entidad_procedencia;
            }
            
            $servicioId = null;
            $servicioOtro = null;
            
            if ($request->servicio === 'otro') {
                $servicioOtro = strtoupper($request->servicio_otro);
            } else {
                $servicioId = (int) $request->servicio;
            }
            
            // Insertar solicitud
            $solicitudId = DB::table('solicitudes_servicios')->insertGetId([
                'nombres' => strtoupper($request->nombres),
                'apellido_paterno' => strtoupper($request->apellido_paterno),
                'apellido_materno' => $request->apellido_materno ? strtoupper($request->apellido_materno) : null,
                'telefono' => $request->telefono,
                'correo_electronico' => strtolower($request->correo_electronico),
                'entidad_procedencia_id' => $entidadId,
                'entidad_otra' => $entidadOtra,
                'servicio_id' => $servicioId,
                'servicio_otro' => $servicioOtro,
                'coordinacion_id' => (int) $request->coordinacion,
                'motivo_solicitud' => $request->motivo_solicitud,
                'estatus' => 'pendiente',
                'fecha_solicitud' => now(),
                'fecha_actualizacion' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Solicitud enviada correctamente. Su número de folio es: ' . $solicitudId,
                'folio' => $solicitudId
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Muestra todas las solicitudes (opcional - para administración)
     */
    public function listar()
    {
        $solicitudes = DB::table('solicitudes_servicios as s')
            ->leftJoin('entidades_procedencia as e', 's.entidad_procedencia_id', '=', 'e.id')
            ->leftJoin('servicios as sv', 's.servicio_id', '=', 'sv.id')
            ->join('coordinaciones as c', 's.coordinacion_id', '=', 'c.id')
            ->select(
                's.id',
                DB::raw("CONCAT(s.nombres, ' ', s.apellido_paterno, IFNULL(CONCAT(' ', s.apellido_materno), '')) as solicitante"),
                's.telefono',
                's.correo_electronico',
                DB::raw("COALESCE(s.entidad_otra, e.nombre, 'N/A') as entidad"),
                DB::raw("COALESCE(s.servicio_otro, sv.nombre, 'N/A') as servicio"),
                'c.nombre as coordinacion',
                's.motivo_solicitud',
                's.estatus',
                's.fecha_solicitud'
            )
            ->orderBy('s.fecha_solicitud', 'desc')
            ->paginate(20);
        
        return view('solicitud.listar', compact('solicitudes'));
    }
}