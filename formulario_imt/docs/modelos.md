# Modelos Eloquent

## Servicio (`servicios`)
- Fillable: `nombre`, `coordinacion_predeterminada_id`, `fecha_creacion`.
- Timestamps: `false`.

## Coordinacion (`coordinaciones`)
- Fillable: `nombre`, `correo`, `coordinador`, `fecha_creacion`.
- Timestamps: `false`.

## EntidadProcedencia (`entidades_procedencia`)
- Fillable: `nombre`, `activo`, `fecha_creacion`.
- Timestamps: `false`.

## SolicitudServicio (`solicitudes_servicios`)
- Fillable: entidad/coordinación, contacto (nombre, correo, teléfono), servicio, notas.
- Timestamps: `false`.

## Correo (`correos`)
- Fillable: `tipo`, `titulo`, `cuerpo`, `despedida`.

## User
- Fillable: `name`, `email`, `password`.
- Hidden/Casts según configuración del modelo.