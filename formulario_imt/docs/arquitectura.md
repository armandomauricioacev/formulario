# Arquitectura

- Framework: `Laravel` (aplicación web con rutas y controladores).
- Controladores: `FormularioController` gestiona vistas y endpoints JSON.
- Modelos Eloquent:
  - `Servicio` (`servicios`)
  - `Coordinacion` (`coordinaciones`)
  - `EntidadProcedencia` (`entidades_procedencia`)
  - `SolicitudServicio` (`solicitudes_servicios`)
  - `Correo` (`correos`)
  - `User` (auth)
- Vistas: Plantilla principal del formulario en Blade (carga vía controlador).
- Activos: Vite + Tailwind, entradas `resources/css/app.css` y `resources/js/app.js`.
- Configuración:
  - Rutas web: `routes/web.php`
  - Middleware adicional: `App\Http\Middleware\SecureHeaders` en grupo `web`.
  - Excepciones y pipeline por `bootstrap/app.php`.

## Flujo de solicitud
- Usuario accede a `/solicitud-servicios` (o `/solicitud`).
- Consulta catálogos (servicios, coordinaciones, entidades) vía endpoints JSON cuando aplica.
- Envío de formulario usando `POST /solicitud/store`, con persistencia y envío de correos.
- Listado y filtro de solicitudes en `/solicitudes` y `/solicitudes/data`.