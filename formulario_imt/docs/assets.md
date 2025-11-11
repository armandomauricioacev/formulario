# Activos Front‑End y Recursos

- Vite:
  - Configuración en `vite.config.js`.
  - Entradas: `resources/css/app.css`, `resources/js/app.js`.
  - Plugin: `laravel-vite-plugin` y `@tailwindcss/vite`.

- Tailwind CSS:
  - Integrado mediante plugin Vite.
  - Estilos aplicables al formulario sin alterar diseño actual.

- Blade:
  - Vista principal renderizada por `FormularioController@index`.
  - `welcome.blade.php` contiene SVG/markup demostrativo.