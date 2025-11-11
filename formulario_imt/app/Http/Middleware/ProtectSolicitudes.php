<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProtectSolicitudes
{
    /**
     * Restringe acceso a rutas de listado de solicitudes según configuración.
     * - En producción: si SOLICITUDES_PROTECT=ip, solo permite IPs en SOLICITUDES_ALLOWLIST.
     * - En otros entornos o si está en "off": permite acceso.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $protectMode = env('SOLICITUDES_PROTECT', 'off');
        $isProd = app()->environment('production');

        if ($isProd && $protectMode === 'ip') {
            $allowlist = array_filter(array_map('trim', explode(',', (string) env('SOLICITUDES_ALLOWLIST', ''))));
            $ip = $request->ip();
            if (!in_array($ip, $allowlist, true)) {
                return response('Acceso restringido.', 403);
            }
        }

        return $next($request);
    }
}