<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para la tabla "servicios".
 *
 * Catálogo de servicios disponibles. No usa timestamps.
 * Campos asignables: nombre, coordinacion_predeterminada_id, fecha_creacion.
 */
class Servicio extends Model
{
    protected $table = 'servicios';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'coordinacion_predeterminada_id',
        'fecha_creacion',
    ];
}