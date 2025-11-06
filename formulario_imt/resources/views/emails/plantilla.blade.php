<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f5f6f8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif; color: #111827; }
        .wrapper { width: 100%; padding: 24px 0; }
        .card { max-width: 720px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; }
        .content { padding: 28px; text-align: center; }
        h2 { font-size: 24px; margin: 0 0 20px 0; color: #0f172a; }
        p { font-size: 16px; line-height: 1.6; margin: 12px 0; color: #374151; }
        .footer { border-top: 1px solid #e5e7eb; margin-top: 20px; padding-top: 16px; }
        small { color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="content">
                <h2>{{ $titulo }}</h2>
                @foreach ($lineas as $line)
                    @if (trim($line) !== '')
                        <p>{{ $line }}</p>
                    @endif
                @endforeach
                @if (!empty($despedida))
                    <p style="margin-top: 20px; font-weight: 600;">{{ $despedida }}</p>
                @endif
                <div class="footer">
                    <small>Este mensaje fue generado automáticamente por el sistema de solicitudes.</small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>