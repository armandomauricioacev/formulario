<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('correos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 30)->unique(); // solicitante, coordinador, asistente, representante
            $table->string('titulo')->nullable();
            $table->longText('cuerpo')->nullable();
            $table->string('despedida')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('correos');
    }
};