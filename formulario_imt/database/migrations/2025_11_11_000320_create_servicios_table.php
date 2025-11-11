<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 200)->unique();
            $table->unsignedInteger('coordinacion_predeterminada_id');
            $table->timestamp('fecha_creacion')->nullable()->useCurrent();

            $table->index('nombre', 'idx_nombre');
            $table->index('coordinacion_predeterminada_id', 'idx_coordinacion');

            $table->foreign('coordinacion_predeterminada_id')
                  ->references('id')->on('coordinaciones')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};