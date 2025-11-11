<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('solicitudes_servicios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombres', 60);
            $table->string('apellido_paterno', 60);
            $table->string('apellido_materno', 60)->nullable();
            $table->string('telefono', 10);
            $table->string('correo_electronico', 100);

            $table->unsignedInteger('entidad_procedencia_id')->nullable();
            $table->string('entidad_otra', 200)->nullable();

            $table->unsignedInteger('servicio_id')->nullable();
            $table->string('servicio_otro', 200)->nullable();

            $table->unsignedInteger('coordinacion_id');

            $table->longText('motivo_solicitud');

            $table->enum('estatus', ['en_revision', 'revisado'])->default('en_revision');
            $table->timestamp('fecha_solicitud')->nullable()->useCurrent();
            $table->timestamp('fecha_actualizacion')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index('nombres', 'idx_nombres');
            $table->index(['apellido_paterno', 'apellido_materno'], 'idx_apellidos');
            $table->index('telefono', 'idx_telefono');
            $table->index('correo_electronico', 'idx_correo');
            $table->index('estatus', 'idx_estatus');
            $table->index('fecha_solicitud', 'idx_fecha');
            $table->index('entidad_procedencia_id', 'idx_entidad');
            $table->index('servicio_id', 'idx_servicio');
            $table->index('coordinacion_id', 'idx_coordinacion');

            $table->foreign('entidad_procedencia_id')
                ->references('id')->on('entidades_procedencia')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('servicio_id')
                ->references('id')->on('servicios')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('coordinacion_id')
                ->references('id')->on('coordinaciones')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_servicios');
    }
};