<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coordinacion extends Model
{
    protected $table = 'coordinaciones';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
        'fecha_creacion',
    ];
}