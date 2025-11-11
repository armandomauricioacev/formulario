# Instalación y Configuración

- Requisitos:
  - PHP compatible con Laravel.
  - Extensiones habituales (PDO, mbstring, etc.).
  - Node.js para Vite/Tailwind.

- Pasos sugeridos:
  - Configurar `.env` (DB, `APP_URL`, `APP_TIMEZONE`, `APP_LOCALE`).
  - Instalar dependencias PHP y Node.
  - Migrar/crear tablas requeridas (según modelos existentes en BD).
  - Levantar servidor y Vite.

- Notas:
  - Middleware `SecureHeaders` añadido al grupo `web` en `bootstrap/app.php`.
  - `APP_TIMEZONE` predeterminado: `America/Mexico_City`.