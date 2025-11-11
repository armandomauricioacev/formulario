<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoordinacionesSeeder extends Seeder
{
    public function run(): void
    {
        $nombres = [
            'Coordinación de Desarrollo',
            'Coordinación de Infraestructura',
            'Coordinación de Soporte',
            'Coordinación de Telemática',
        ];

        foreach ($nombres as $nombre) {
            $existe = DB::table('coordinaciones')->where('nombre', $nombre)->exists();
            if (!$existe) {
                DB::table('coordinaciones')->insert(['nombre' => $nombre]);
            }
        }
    }
}