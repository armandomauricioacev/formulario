<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para la tabla "entidades_procedencia".
 *
 * Catálogo de entidades de procedencia. No usa timestamps.
 * Campos asignables: nombre, activo, fecha_creacion.
 */
class EntidadProcedencia extends Model
{
    protected $table = 'entidades_procedencia';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'activo',
        'fecha_creacion',
    ];
}