<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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