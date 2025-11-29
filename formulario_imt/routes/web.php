<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\FormularioController;
use Illuminate\Support\Facades\Artisan;

/**
 * Definición de rutas web para la Solicitud de Servicios IMT.
 *
 * Responsabilidades principales:
 * - Mostrar el formulario de solicitud y guardar datos.
 * - Proveer catálogos (servicios, coordinaciones, entidades) vía JSON.
 * - Entregar coordinación predeterminada para un servicio (AJAX).
 * - Listado y datos de solicitudes con filtros.
 *
 * Nota: Todas las rutas mantienen la lógica y diseño existentes.
 */

/*
|--------------------------------------------------------------------------
| Web Routes - Solicitud de Servicios IMT
|--------------------------------------------------------------------------
*/

// Página raíz: sirve directamente la vista principal del formulario (sin controlador)
Route::get('/', function () {
    return view('forms.solicitud-servicios');
})->name('home');

// Vista principal del formulario de solicitud (sin controlador)
// Método: GET
// Respuesta: HTML (Blade)
Route::get('/solicitud-servicios', function () {
    return view('forms.solicitud-servicios');
})->name('solicitud-servicios');

// Alias de la vista principal (sin controlador)
// Método: GET
// Respuesta: HTML (Blade)
Route::get('/solicitud', function () {
    return view('forms.solicitud-servicios');
})->name('solicitud.index');

// Persistencia de una solicitud de servicio
// Método: POST
// Middleware: throttle (30 req/min por IP)
// Respuesta: JSON con éxito/mensaje
Route::post('/solicitud/store', [FormularioController::class, 'store'])
    ->middleware('throttle:30,1') // 30 peticiones por minuto por IP
    // Desactivar CSRF únicamente para este endpoint de envío
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('solicitud.store');

// Coordinación predeterminada para un servicio
// Método: GET
// Parámetros: {servicioId}
// Respuesta: JSON con id y nombre de coordinación
Route::get('/solicitud/coordinacion/{servicioId}', [FormularioController::class, 'coordinacion'])
    ->name('solicitud.coordinacion');

// Catálogos disponibles en JSON
// Método: GET
// Respuesta: JSON (lista)
Route::get('/coordinaciones', [FormularioController::class, 'coordinaciones'])->name('coordinaciones.index');
Route::get('/entidades', [FormularioController::class, 'entidades'])->name('entidades.index');
Route::get('/servicios', [FormularioController::class, 'servicios'])->name('servicios.index');

// Listado y datos de solicitudes
// /solicitudes: HTML (vista con filtros)
// /solicitudes/data: JSON (paginado y filtros)
Route::get('/solicitudes', [FormularioController::class, 'solicitudesIndex'])
    ->middleware('protect.solicitudes')
    ->name('solicitudes.index');
Route::get('/solicitudes/data', [FormularioController::class, 'solicitudesData'])
    ->middleware('throttle:60,1') // 60 peticiones por minuto por IP
    ->middleware('protect.solicitudes')
    ->name('solicitudes.data');

// Debug y consola: registrar rutas solo en entorno local
// Rutas de debug/console removidas por solicitud del usuario

// Endpoint de refresco de configuración eliminado según solicitud del usuario
