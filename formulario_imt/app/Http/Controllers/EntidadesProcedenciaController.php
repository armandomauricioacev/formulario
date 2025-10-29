<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class EntidadesProcedenciaController extends Controller
{
    /**
     * Devuelve entidades de procedencia activas en formato JSON.
     */
    public function index()
    {
        $entidades = DB::table('entidades_procedencia')
            ->where('activo', 1)
            ->orderBy('nombre', 'asc')
            ->get(['id', 'nombre']);

        return response()->json($entidades);
    }
}