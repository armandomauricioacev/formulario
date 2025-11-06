<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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