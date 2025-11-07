@extends('forms.layouts.base')

@section('content')
<div class="container py-3">
    <h1 class="h4 mb-3">Listado de solicitudes</h1>

    <form id="filters" class="mb-3">
        <div class="row g-2">
            <div class="col-md-3">
                <label for="q" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="q" name="q" placeholder="Nombre, correo, servicio...">
            </div>
            <div class="col-md-2">
                <label for="estatus" class="form-label">Estatus</label>
                <select class="form-select" id="estatus" name="estatus">
                    <option value="">Todos</option>
                    @foreach($estatus as $e)
                        <option value="{{ $e }}">{{ $e }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="servicio_id" class="form-label">Servicio</label>
                <select class="form-select" id="servicio_id" name="servicio_id">
                    <option value="">Todos</option>
                    @foreach($servicios as $s)
                        <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="coordinacion_id" class="form-label">Coordinación</label>
                <select class="form-select" id="coordinacion_id" name="coordinacion_id">
                    <option value="">Todas</option>
                    @foreach($coordinaciones as $c)
                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <label for="per_page" class="form-label">Por página</label>
                <select class="form-select" id="per_page" name="per_page">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
        <div class="mt-2">
            <button type="button" id="clear" class="btn btn-outline-secondary btn-sm">Limpiar filtros</button>
        </div>
    </form>

    <div id="results">
        <div class="text-muted">Cargando...</div>
    </div>
</div>

<script>
(function(){
    const form = document.getElementById('filters');
    const results = document.getElementById('results');
    const inputs = ['q','estatus','servicio_id','coordinacion_id','per_page'].map(id => document.getElementById(id));

    const debounce = (fn, delay=350) => {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), delay);
        };
    };

    function getParams(){
        const params = new URLSearchParams();
        inputs.forEach(el => {
            if (!el) return;
            const val = (el.value || '').trim();
            if (val) params.set(el.name, val);
        });
        return params;
    }

    function setFormFromSearch(search){
        const params = new URLSearchParams(search);
        inputs.forEach(el => {
            if (!el) return;
            el.value = params.get(el.name) || '';
        });
    }

    async function fetchData(opts = { push:true }){
        const params = getParams();
        const url = '/solicitudes/data?' + params.toString();
        try {
            const resp = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
            const html = await resp.text();
            results.innerHTML = html;
            if (opts.push) {
                const listUrl = '/solicitudes?' + params.toString();
                history.pushState({ listUrl }, '', listUrl);
            }
            wirePagination();
        } catch (e) {
            results.innerHTML = '<div class="alert alert-danger">Error cargando datos.</div>';
            console.error(e);
        }
    }

    function wirePagination(){
        results.querySelectorAll('a.page-link, .pagination a').forEach(a => {
            a.addEventListener('click', (ev) => {
                ev.preventDefault();
                const href = a.getAttribute('href');
                if (!href) return;
                const url = new URL(href, window.location.origin);
                const dataUrl = '/solicitudes/data?' + url.searchParams.toString();
                fetch(dataUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                    .then(r => r.text())
                    .then(html => {
                        results.innerHTML = html;
                        const listUrl = '/solicitudes?' + url.searchParams.toString();
                        history.pushState({ listUrl }, '', listUrl);
                        wirePagination();
                    }).catch(err => {
                        console.error(err);
                        results.innerHTML = '<div class="alert alert-danger">Error cargando página.</div>';
                    });
            });
        });
    }

    // Events
    const onChange = debounce(() => fetchData({ push:true }));
    document.getElementById('q').addEventListener('input', onChange);
    ['estatus','servicio_id','coordinacion_id','per_page'].forEach(id => {
        const el = document.getElementById(id);
        el.addEventListener('change', () => fetchData({ push:true }));
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        fetchData({ push:true });
    });

    document.getElementById('clear').addEventListener('click', () => {
        inputs.forEach(el => { if (el) el.value = ''; });
        fetchData({ push:true });
    });

    window.addEventListener('popstate', (ev) => {
        setFormFromSearch(location.search);
        fetchData({ push:false });
    });

    // Init
    setFormFromSearch(location.search);
    fetchData({ push:false });
})();
</script>
@endsection