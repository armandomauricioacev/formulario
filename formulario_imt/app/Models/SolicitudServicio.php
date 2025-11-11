<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para la tabla "solicitudes_servicios".
 *
 * Representa una solicitud de servicio. No usa timestamps.
 * Campos asignables incluyen entidad/coordinación, datos de contacto y notas.
 */
class SolicitudServicio extends Model
{
    protected $table = 'solicitudes_servicios';
    public $timestamps = false;

    protected $fillable = [
        'entidad_procedencia_id',
        'nombre_contacto',
        'cargo',
        'servicio_solicitado_id',
        'servicio_solicitado_nombre_otra_opcion',
        'coordinacion_id',
        'coordinacion_nombre_otra_opcion',
        'telefono',
        'email',
        'notas_adicionales',
        'fecha_solicitud',
        'fecha_actualizacion',
    ];
}