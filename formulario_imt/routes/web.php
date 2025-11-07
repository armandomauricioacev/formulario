<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\FormularioController;

/*
|--------------------------------------------------------------------------
| Web Routes - Solicitud de Servicios IMT
|--------------------------------------------------------------------------
*/

// Ruta raíz - Redirigir a /solicitud-servicios
Route::redirect('/', '/solicitud-servicios');

// Vista principal explícita en /solicitud-servicios
Route::get('/solicitud-servicios', [FormularioController::class, 'index'])
    ->name('solicitud-servicios');

// Ruta alternativa /solicitud apuntando a la misma vista
Route::get('/solicitud', [FormularioController::class, 'index'])
    ->name('solicitud.index');

// Ruta para guardar la solicitud
Route::post('/solicitud/store', [FormularioController::class, 'store'])
    ->name('solicitud.store');

// Ruta para obtener coordinación predeterminada de un servicio (AJAX)
Route::get('/solicitud/coordinacion/{servicioId}', [FormularioController::class, 'coordinacion'])
    ->name('solicitud.coordinacion');

// Endpoints JSON opcionales para catálogos
Route::get('/coordinaciones', [FormularioController::class, 'coordinaciones'])->name('coordinaciones.index');
Route::get('/entidades', [FormularioController::class, 'entidades'])->name('entidades.index');
Route::get('/servicios', [FormularioController::class, 'servicios'])->name('servicios.index');

// Listado de solicitudes con filtros en tiempo real
Route::get('/solicitudes', [FormularioController::class, 'solicitudesIndex'])->name('solicitudes.index');
Route::get('/solicitudes/data', [FormularioController::class, 'solicitudesData'])->name('solicitudes.data');
