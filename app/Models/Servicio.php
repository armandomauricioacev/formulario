<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicios';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'coordinacion_predeterminada_id',
        'descripcion',
        'activo',
        'fecha_creacion',
    ];
}