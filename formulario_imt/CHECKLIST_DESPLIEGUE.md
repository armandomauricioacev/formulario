# Checklist de Despliegue (Producción / Plesk)

## 1) Dependencias requeridas
- PHP 8.2+ con extensiones: `openssl`, `pdo`, `mbstring`, `tokenizer`, `ctype`, `json`, `bcmath`.
- Composer instalado en el servidor.
- Node.js (para construir assets), o construir localmente y subir `public/build`.
- Servidor web apuntando el DocumentRoot al directorio `public/` del proyecto.

## 2) Pasos secuenciales de despliegue
- Subir el proyecto al servidor y apuntar el dominio a `public/`.
- Ejecutar en la raíz del proyecto:
  - `composer install --no-dev --optimize-autoloader`
  - Copiar `.env.example` a `.env` y completar variables (`APP_*`, `DB_*`, `MAIL_*`).
  - `php artisan key:generate`
  - `php artisan migrate --force`
  - Construir assets con Vite:
    - Opcional A (servidor): `npm ci && npm run build`
    - Opcional B (local): construir localmente y subir `public/build`
  - Cachear configuración y rutas:
    - `php artisan config:cache && php artisan route:cache && php artisan view:cache`

## 3) Configuraciones críticas a validar
- `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://tu-dominio`.
- Conexión a base de datos (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
- Correo SMTP (`MAIL_HOST`, `MAIL_PORT`, `MAIL_ENCRYPTION`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_*`).
- Sesiones: `SESSION_SECURE_COOKIE=true`, `SESSION_SAME_SITE=lax`.
- Permisos de escritura: `storage/` y `bootstrap/cache/`.
- Cabeceras de seguridad: habilitadas en producción desde middleware.

## 4) Pruebas post-despliegue obligatorias
- Acceso a `https://tu-dominio/solicitud-servicios` y carga sin errores.
- Completar y enviar el formulario; confirmar que llega correo al solicitante y a coordinación.
- Probar validaciones (teléfono 10 dígitos, correos válidos, casos "Otra entidad" y "Otro servicio").
- Revisar logs: `storage/logs/laravel.log` (sin errores críticos).

## 5) Contactos de soporte (emergencias)
- Infraestructura/Servidor: Nombre, correo, teléfono.
- Aplicación/Formulario: Nombre, correo, teléfono.
- Correo/SMTP: Nombre, correo, teléfono.