<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntidadesProcedenciaSeeder extends Seeder
{
    public function run(): void
    {
        $nombres = [
            'Universidad Politécnica de Querétaro',
            'Centro de Investigación y Desarrollo Tecnológico',
        ];

        foreach ($nombres as $nombre) {
            $existe = DB::table('entidades_procedencia')->where('nombre', $nombre)->exists();
            if (!$existe) {
                DB::table('entidades_procedencia')->insert(['nombre' => $nombre]);
            }
        }
    }
}