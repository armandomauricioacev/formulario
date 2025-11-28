<?php
/**
 * Router para servidor PHP integrado (desarrollo local).
 *
 * Objetivo: al entrar a la raíz, redirige a la vista principal
 * del formulario (ruta /solicitud-servicios) y sirve estáticos
 * directamente desde /public. El resto lo delega a public/index.php.
 *
 * Uso:
 *   php -S 127.0.0.1:8003 server.php
 *   (ejecutar en el directorio raíz del proyecto)
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$base = __DIR__;
$public = __DIR__ . '/public';

// Redirigir raíz a la vista principal del formulario
if ($uri === '/' || $uri === '' ) {
    header('Location: /solicitud-servicios');
    exit;
}

// Si el recurso existe en /public, servirlo directamente (CSS/JS/imagenes)
if ($uri !== '/' && file_exists($public . $uri) && is_file($public . $uri)) {
    return false;
}

// Todo lo demás: delegar al index.php de /public (Laravel)
require_once $public . '/index.php';

