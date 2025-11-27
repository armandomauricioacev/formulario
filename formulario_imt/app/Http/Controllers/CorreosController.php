<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class CorreosController extends Controller
{
    public function index()
    {
        $templates = [
            'solicitante' => ['subject' => '', 'body' => '', 'farewell' => ''],
            'coordinador' => ['subject' => '', 'body' => '', 'farewell' => ''],
            'asistente'   => ['subject' => '', 'body' => '', 'farewell' => ''],
            'representante' => ['subject' => '', 'body' => '', 'farewell' => ''],
        ];

        if (Schema::hasTable('correos')) {
            try {
                $rows = DB::table('correos')->get();
                foreach ($rows as $row) {
                    $tipo = $row->tipo;
                    if (isset($templates[$tipo])) {
                        $templates[$tipo]['subject'] = (string) ($row->subject ?? '');
                        $templates[$tipo]['body'] = (string) ($row->body ?? '');
                        $templates[$tipo]['farewell'] = (string) ($row->farewell ?? '');
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('[Correos] No se pudieron leer plantillas', ['error' => $e->getMessage()]);
            }
        }

        return view('admin.correos', compact('templates'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'subject_solicitante'     => ['nullable', 'string', 'max:255'],
            'body_solicitante'        => ['nullable', 'string', 'max:5000'],
            'farewell_solicitante'    => ['nullable', 'string', 'max:255'],

            'subject_coordinador'     => ['nullable', 'string', 'max:255'],
            'body_coordinador'        => ['nullable', 'string', 'max:5000'],
            'farewell_coordinador'    => ['nullable', 'string', 'max:255'],

            'subject_asistente'       => ['nullable', 'string', 'max:255'],
            'body_asistente'          => ['nullable', 'string', 'max:5000'],
            'farewell_asistente'      => ['nullable', 'string', 'max:255'],

            'subject_representante'   => ['nullable', 'string', 'max:255'],
            'body_representante'      => ['nullable', 'string', 'max:5000'],
            'farewell_representante'  => ['nullable', 'string', 'max:255'],
        ]);

        if (!Schema::hasTable('correos')) {
            return back()->with('error', 'La tabla de correos no existe. Ejecuta las migraciones.');
        }

        $map = [
            'solicitante' => [
                'subject' => $data['subject_solicitante'] ?? null,
                'body' => $data['body_solicitante'] ?? null,
                'farewell' => $data['farewell_solicitante'] ?? null,
            ],
            'coordinador' => [
                'subject' => $data['subject_coordinador'] ?? null,
                'body' => $data['body_coordinador'] ?? null,
                'farewell' => $data['farewell_coordinador'] ?? null,
            ],
            'asistente' => [
                'subject' => $data['subject_asistente'] ?? null,
                'body' => $data['body_asistente'] ?? null,
                'farewell' => $data['farewell_asistente'] ?? null,
            ],
            'representante' => [
                'subject' => $data['subject_representante'] ?? null,
                'body' => $data['body_representante'] ?? null,
                'farewell' => $data['farewell_representante'] ?? null,
            ],
        ];

        try {
            foreach ($map as $tipo => $fields) {
                DB::table('correos')->updateOrInsert(
                    ['tipo' => $tipo],
                    array_merge($fields, [
                        'updated_at' => now(),
                        'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                    ])
                );
            }
        } catch (\Throwable $e) {
            Log::error('[Correos] Falló actualización de plantillas', ['error' => $e->getMessage()]);
            return back()->with('error', 'No se pudieron guardar las plantillas.');
        }

        return back()->with('success', 'Plantillas de correos actualizadas correctamente.');
    }
}

