# Controladores

## FormularioController
- `index(Request $request)`
  - Renderiza la vista con catálogos precargados.
  - Manejo de errores: `try/catch` para lecturas; usa colecciones vacías si falla.
- `store(Request $request)`
  - Valida entrada, persiste solicitud y envía correos.
  - Atomicidad: `DB::transaction` y `try/catch` para QueryException/Throwable.
  - Respuesta: JSON (éxito/mensaje); no cambia contrato existente.
- `servicios()`
  - Retorna catálogo de servicios en JSON.
  - Manejo de errores: `try/catch` con logging y 500.
- `coordinacion(int $servicioId)`
  - Obtiene coordinación predeterminada de un servicio.
  - Diferencia 404 (no encontrado) vs 500 (error de consulta).
- `coordinaciones()`
  - Lista de coordinaciones (JSON) con manejo de errores.
- `entidades()`
  - Lista de entidades de procedencia (JSON) con manejo de errores.
- `enviarPorSolicitud(array $payload)`
  - Auxiliar para envío de correo; protege lecturas iniciales.
- `solicitudesIndex(Request $request)`
  - Vista con filtros; errores retornan colecciones vacías.
- `solicitudesData(Request $request)`
  - JSON paginado/filtrado; `try/catch` con 500 en fallo.

## Controller (base)
- Clase abstracta base de controladores.