<?php
/**
 * Router/Bootstrap para desarrollo local cuando el servidor apunta al raíz.
 * Sirve todo desde /public y deja estáticos intactos.
 *
 * Útil en Laragon si el VirtualHost está apuntando al directorio del proyecto
 * en lugar de a /public. Coloca este archivo en la raíz del proyecto.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$base = __DIR__;
$public = __DIR__ . '/public';

// Si el recurso existe en el raíz (por ejemplo, README.md), lo sirve Apache/Nginx directamente
if ($uri !== '/' && file_exists($base . $uri)) {
    return false;
}

// Si el recurso existe en /public (CSS/JS/imagenes), lo sirve directamente
if ($uri !== '/' && file_exists($public . $uri)) {
    return false;
}

// Todo lo demás: delegar al index.php de /public
require_once $public . '/index.php';

