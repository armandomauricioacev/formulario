<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiciosSeeder extends Seeder
{
    public function run(): void
    {
        $coordinacionesMap = [];
        foreach ([
            'Coordinación de Desarrollo',
            'Coordinación de Infraestructura',
            'Coordinación de Soporte',
            'Coordinación de Telemática',
        ] as $nombreCoord) {
            $id = DB::table('coordinaciones')->where('nombre', $nombreCoord)->value('id');
            if ($id) $coordinacionesMap[$nombreCoord] = $id;
        }

        $servicios = [
            ['nombre' => 'Calibración de Equipos', 'coord' => 'Coordinación de Infraestructura'],
            ['nombre' => 'Mantenimiento de Infraestructura', 'coord' => 'Coordinación de Infraestructura'],
            ['nombre' => 'Certificación de Equipos', 'coord' => 'Coordinación de Infraestructura'],
            ['nombre' => 'Desarrollo de Software', 'coord' => 'Coordinación de Desarrollo'],
            ['nombre' => 'Consultoría Tecnológica', 'coord' => 'Coordinación de Desarrollo'],
            ['nombre' => 'Investigación Aplicada', 'coord' => 'Coordinación de Desarrollo'],
            ['nombre' => 'Capacitación Técnica', 'coord' => 'Coordinación de Soporte'],
            ['nombre' => 'Soporte Especializado', 'coord' => 'Coordinación de Soporte'],
            ['nombre' => 'Asesoría Profesional', 'coord' => 'Coordinación de Soporte'],
            ['nombre' => 'Servicios del Pepe', 'coord' => 'Coordinación de Telemática'],
        ];

        foreach ($servicios as $srv) {
            $existe = DB::table('servicios')->where('nombre', $srv['nombre'])->exists();
            $coordId = $coordinacionesMap[$srv['coord']] ?? null;
            if (!$existe && $coordId) {
                DB::table('servicios')->insert([
                    'nombre' => $srv['nombre'],
                    'coordinacion_predeterminada_id' => $coordId,
                ]);
            }
        }
    }
}