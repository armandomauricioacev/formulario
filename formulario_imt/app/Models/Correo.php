<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para la tabla "correos".
 *
 * Define plantillas por tipo (solicitante, coordinador, asistente, representante).
 * Campos asignables: tipo, titulo, cuerpo, despedida.
 */
class Correo extends Model
{
    protected $table = 'correos';

    protected $fillable = [
        'tipo', // solicitante, coordinador, asistente, representante
        'titulo',
        'cuerpo',
        'despedida',
    ];
}