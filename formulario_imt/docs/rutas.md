# Rutas Principales

- `GET /solicitud-servicios`
  - Muestra la vista principal del formulario.
  - Controlador: `FormularioController@index`
  - Respuesta: HTML (Blade)

- `GET /solicitud`
  - Alias hacia la misma vista del formulario.
  - Controlador: `FormularioController@index`
  - Respuesta: HTML

- `POST /solicitud/store`
  - Guarda una solicitud de servicio.
  - Controlador: `FormularioController@store`
  - Middleware: `throttle:30,1`
  - Respuesta: JSON (estado y mensajes)

- `GET /solicitud/coordinacion/{servicioId}`
  - Obtiene coordinación predeterminada para el servicio indicado.
  - Controlador: `FormularioController@coordinacion`
  - Parámetros: `servicioId` (numérico)
  - Respuesta: JSON (id/nombre)

- `GET /servicios`
  - Lista de servicios (JSON).
  - Controlador: `FormularioController@servicios`

- `GET /coordinaciones`
  - Lista de coordinaciones (JSON).
  - Controlador: `FormularioController@coordinaciones`

- `GET /entidades`
  - Lista de entidades de procedencia (JSON).
  - Controlador: `FormularioController@entidades`

- `GET /solicitudes`
  - Vista con filtros para solicitudes.
  - Controlador: `FormularioController@solicitudesIndex`
  - Respuesta: HTML

- `GET /solicitudes/data`
  - Datos paginados y filtrados de solicitudes.
  - Controlador: `FormularioController@solicitudesData`
  - Respuesta: JSON