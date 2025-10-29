<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('forms.solicitud-servicios');
})->name('solicitud-servicios');
