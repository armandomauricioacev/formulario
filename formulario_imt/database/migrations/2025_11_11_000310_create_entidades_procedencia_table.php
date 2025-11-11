<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('entidades_procedencia', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 200)->unique();
            $table->timestamp('fecha_creacion')->nullable()->useCurrent();
            $table->index('nombre', 'idx_nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entidades_procedencia');
    }
};