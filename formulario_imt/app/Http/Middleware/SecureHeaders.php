<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $contentType = (string) $response->headers->get('Content-Type');
        $isHtml = str_contains(strtolower($contentType), 'text/html') || $contentType === '';

        if ($isHtml) {
            // Seguridad de contenido y privacidad
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            // Restringir APIs del navegador
            $response->headers->set('Permissions-Policy', "geolocation=(), microphone=(), camera=(), accelerometer=(), gyroscope=(), magnetometer=(), usb=(), payment=()");
            // Aislar contextos y mitigar ataques de navegación cruzada
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
            // Mitigar clickjacking (además de frame-ancestors en CSP)
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            // IE/Adobe: bloquear políticas cruzadas
            $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
            // IE: evitar abrir descargas peligrosas
            $response->headers->set('X-Download-Options', 'noopen');

            // CSP solo en producción para evitar ruido en consola local.
            // En producción se aplica en modo "enforce" y con upgrade-insecure-requests.
            if (app()->environment('production')) {
                $csp = implode(' ', [
                    // Núcleo
                    "default-src 'self' https: data: blob:",
                    // Scripts
                    "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://framework-gb.cdn.gob.mx https://cdn.jsdelivr.net",
                    "script-src-elem 'self' https://framework-gb.cdn.gob.mx https://cdn.jsdelivr.net",
                    // Estilos
                    "style-src 'self' 'unsafe-inline' https://framework-gb.cdn.gob.mx https://fonts.googleapis.com",
                    "style-src-elem 'self' 'unsafe-inline' https://framework-gb.cdn.gob.mx https://fonts.googleapis.com",
                    // Fuentes
                    "font-src 'self' https://fonts.gstatic.com data:",
                    // Imágenes y conexiones
                    "img-src 'self' data: https:",
                    "connect-src 'self' https:",
                    // Formularios y navegación
                    "form-action 'self'",
                    "base-uri 'self'",
                    // Objetos e incrustaciones
                    "object-src 'none'",
                    // Framing
                    "frame-ancestors 'self'",
                    "frame-src 'self' https:",
                    // Upgrade de recursos inseguros
                    "upgrade-insecure-requests",
                ]);
                $response->headers->set('Content-Security-Policy', $csp);

                // HSTS solo en producción y sobre HTTPS
                if ($request->isSecure()) {
                    $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
                }
            }

            // Cache: evitar almacenamiento de páginas dinámicas
            $response->headers->set('Cache-Control', 'private, no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}