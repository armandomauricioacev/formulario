<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CoordinacionesController extends Controller
{
    /**
     * Devuelve coordinaciones en formato JSON.
     */
    public function index()
    {
        $coordinaciones = DB::table('coordinaciones')
            ->orderBy('nombre', 'asc')
            ->get(['id', 'nombre']);

        return response()->json($coordinaciones);
    }
}