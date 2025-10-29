<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ServiciosController extends Controller
{
    /**
     * Devuelve servicios activos en formato JSON.
     */
    public function index()
    {
        $servicios = DB::table('servicios')
            ->where('activo', 1)
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
}