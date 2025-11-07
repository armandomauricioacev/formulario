<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Hacer coordinacion_id nullable en solicitudes_servicios
        DB::statement('ALTER TABLE `solicitudes_servicios` DROP FOREIGN KEY `solicitudes_servicios_ibfk_3`');
        DB::statement('ALTER TABLE `solicitudes_servicios` MODIFY `coordinacion_id` INT NULL');
        DB::statement('ALTER TABLE `solicitudes_servicios` ADD CONSTRAINT `solicitudes_servicios_ibfk_3` FOREIGN KEY (`coordinacion_id`) REFERENCES `coordinaciones` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
    }

    public function down(): void
    {
        // Revertir: volver a NOT NULL
        DB::statement('ALTER TABLE `solicitudes_servicios` DROP FOREIGN KEY `solicitudes_servicios_ibfk_3`');
        DB::statement('ALTER TABLE `solicitudes_servicios` MODIFY `coordinacion_id` INT NOT NULL');
        DB::statement('ALTER TABLE `solicitudes_servicios` ADD CONSTRAINT `solicitudes_servicios_ibfk_3` FOREIGN KEY (`coordinacion_id`) REFERENCES `coordinaciones` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
    }
};