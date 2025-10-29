<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\SolicitudesServiciosController;
use App\Http\Controllers\CoordinacionesController;
use App\Http\Controllers\EntidadesProcedenciaController;
use App\Http\Controllers\ServiciosController;

/*
|--------------------------------------------------------------------------
| Web Routes - Solicitud de Servicios IMT
|--------------------------------------------------------------------------
*/

// Ruta raíz - Redirigir a /solicitud-servicios
Route::redirect('/', '/solicitud-servicios');

// Vista principal explícita en /solicitud-servicios
Route::get('/solicitud-servicios', [SolicitudesServiciosController::class, 'index'])
    ->name('solicitud-servicios');

// Ruta alternativa /solicitud apuntando a la misma vista
Route::get('/solicitud', [SolicitudesServiciosController::class, 'index'])
    ->name('solicitud.index');

// Ruta para guardar la solicitud
Route::post('/solicitud/store', [SolicitudesServiciosController::class, 'store'])
    ->name('solicitud.store');

// Ruta para obtener coordinación predeterminada de un servicio (AJAX)
Route::get('/solicitud/coordinacion/{servicioId}', [ServiciosController::class, 'coordinacion'])
    ->name('solicitud.coordinacion');

// Ruta opcional para listar solicitudes (administración)
// Endpoints JSON opcionales para catálogos
Route::get('/coordinaciones', [CoordinacionesController::class, 'index'])->name('coordinaciones.index');
Route::get('/entidades', [EntidadesProcedenciaController::class, 'index'])->name('entidades.index');
Route::get('/servicios', [ServiciosController::class, 'index'])->name('servicios.index');