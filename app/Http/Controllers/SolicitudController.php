<?php

namespace App\Http\Controllers;

use App\Models\Coordinacion;
use App\Models\EntidadProcedencia;
use App\Models\Servicio;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        $coordinaciones = Coordinacion::query()
            ->where('activo', 1)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $entidades = EntidadProcedencia::query()
            ->where('activo', 1)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $servicios = Servicio::query()
            ->where('activo', 1)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'coordinacion_predeterminada_id']);

        return view('forms.solicitud-servicios', [
            'coordinaciones' => $coordinaciones,
            'entidades' => $entidades,
            'servicios' => $servicios,
        ]);
    }

    public function obtenerCoordinacionServicio(int $servicioId)
    {
        $servicio = Servicio::query()->find($servicioId);
        if (!$servicio) {
            return response()->json([
                'coordinacion_id' => null,
            ], 404);
        }

        return response()->json([
            'coordinacion_id' => $servicio->coordinacion_predeterminada_id,
        ]);
    }
}