<table class="table table-striped table-sm">
    <thead>
        <tr>
            <th>ID</th>
            <th>Solicitante</th>
            <th>Correo</th>
            <th>Servicio</th>
            <th>Coordinación</th>
            <th>Estatus</th>
        </tr>
    </thead>
    <tbody>
        @forelse($solicitudes as $s)
            <tr>
                <td>{{ $s->id }}</td>
                <td>{{ $s->nombres }} {{ $s->apellido_paterno }} {{ $s->apellido_materno }}</td>
                <td>{{ $s->correo_electronico }}</td>
                <td>{{ $s->servicio_nombre }}</td>
                <td>{{ $s->coordinacion_nombre ?? '—' }}</td>
                <td>{{ $s->estatus }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Sin resultados</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="d-flex justify-content-between align-items-center">
    <div class="small text-muted">
        @if($solicitudes->count())
            Mostrando {{ ($solicitudes->currentPage()-1)*$solicitudes->perPage()+1 }}
            a {{ ($solicitudes->currentPage()-1)*$solicitudes->perPage()+$solicitudes->count() }}
            de {{ $solicitudes->total() }}
        @else
            Mostrando 0 de 0
        @endif
    </div>
    <div>
        {{ $solicitudes->onEachSide(1)->links() }}
    </div>
</div>