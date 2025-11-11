# Manejo de Errores y Transacciones

- Lecturas (catálogos y consultas):
  - En `FormularioController`, se envuelven en `try/catch`.
  - Logging con contexto; vistas reciben colecciones vacías si falla.
  - Endpoints JSON retornan 500 con mensaje controlado.

- Escrituras (persistencia de solicitud):
  - Uso de `DB::transaction` para atomicidad.
  - `try/catch` diferenciado: `QueryException` y `Throwable`.
  - En fallo: rollback, log y respuesta JSON de error.

- Diferenciación de estados:
  - 404: recurso no encontrado (ej. coordinación inexistente).
  - 500: error de consulta/infraestructura.

- Envío de correo:
  - Lecturas previas protegidas.
  - Bloques `try/catch` existentes para envío.