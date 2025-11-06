<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class EntidadesProcedenciaController extends Controller
{
    /**
     * Devuelve entidades de procedencia en formato JSON.
     */
    public function index()
    {
        $entidades = DB::table('entidades_procedencia')
            ->orderBy('nombre', 'asc')
            ->get(['id', 'nombre']);

        return response()->json($entidades);
    }
}