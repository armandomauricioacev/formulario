<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\CoordinacionesSeeder;
use Database\Seeders\EntidadesProcedenciaSeeder;
use Database\Seeders\ServiciosSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Catálogos base para producción
        $this->call([
            CoordinacionesSeeder::class,
            EntidadesProcedenciaSeeder::class,
            ServiciosSeeder::class,
        ]);
    }
}
