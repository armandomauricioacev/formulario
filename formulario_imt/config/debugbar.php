<?php

return [
    // Si es null, seguirá el valor de APP_DEBUG. Para producción usamos env.
    'enabled' => env('DEBUGBAR_ENABLED', null),

    // Rutas excluidas
    'except' => [
        'telescope/*',
        'horizon/*',
        'nova-api/*',
    ],

    // Almacenamiento de datos del Debugbar
    'storage' => [
        'enabled' => true,
        'driver' => 'file',
        'path' => storage_path('debugbar'),
        'connection' => null,
    ],

    // Captura peticiones AJAX/fetch
    'capture_ajax' => true,
    'add_ajax_timing' => true,

    // Restringir por IP en producción
    'allowed_ips' => array_filter(array_map('trim', explode(',', env('DEBUGBAR_ALLOWED_IPS', '')))),
];

