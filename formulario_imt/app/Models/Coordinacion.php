<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para la tabla "coordinaciones".
 *
 * Representa las coordinaciones disponibles. No usa timestamps.
 * Campos asignables: nombre, fecha_creacion.
 */
/**
 * Modelo Eloquent para la tabla "coordinaciones".
 *
 * Catálogo de coordinaciones. No usa timestamps.
 * Campos asignables: nombre, correo, coordinador, fecha_creacion.
 */
class Coordinacion extends Model
{
    protected $table = 'coordinaciones';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'fecha_creacion',
    ];
}