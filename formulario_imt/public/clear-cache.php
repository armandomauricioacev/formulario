<?php
// Limpieza de cachés de Laravel desde una ruta pública.
// Uso: visitar /clear-cache.php (opcional: ?token=TU_TOKEN si existe ADMIN_CONSOLE_TOKEN en .env)

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;

header('Content-Type: text/plain; charset=utf-8');

try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require __DIR__ . '/../bootstrap/app.php';

    /** @var ConsoleKernel $kernel */
    $kernel = $app->make(ConsoleKernel::class);
    $kernel->bootstrap();

    // Si hay token en .env, se requiere para ejecutar.
    $envToken = env('ADMIN_CONSOLE_TOKEN');
    $provided = $_GET['token'] ?? ($_SERVER['HTTP_X_ADMIN_TOKEN'] ?? null);
    if ($envToken && $provided !== $envToken) {
        http_response_code(404);
        echo "Not found";
        exit;
    }

    $commands = [
        'optimize:clear',
        'config:clear',
        'route:clear',
        'view:clear',
        'cache:clear',
    ];

    echo "Laravel cache clear utility\n";
    echo "APP_ENV=" . config('app.env') . "\n";
    echo "Executing: " . implode(', ', $commands) . "\n\n";

    foreach ($commands as $cmd) {
        $exit = $kernel->call($cmd);
        echo sprintf("[%s] exit_code=%d\n", $cmd, $exit);
    }

    echo "\nDone.\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage() . "\n";
}

