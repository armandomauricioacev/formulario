<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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