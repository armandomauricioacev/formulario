<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CoordinacionesController extends Controller
{
    /**
     * Devuelve coordinaciones activas en formato JSON.
     */
    public function index()
    {
        $coordinaciones = DB::table('coordinaciones')
            ->where('activo', 1)
            ->orderBy('nombre', 'asc')
            ->get(['id', 'nombre']);

        return response()->json($coordinaciones);
    }
}