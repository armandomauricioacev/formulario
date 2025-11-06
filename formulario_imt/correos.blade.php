<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Correos electrónicos') }}
        </h2>
    </x-slot>

    <style>
        [x-cloak] { display: none !important; }

        .panel {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        .section-title {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        .field-label { font-size: 14px; color: #374151; margin-bottom: 4px; }
        .field-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
            background: white;
        }
        .field-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        .btn-primary { background: #3b82f6; color: white; padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; font-weight: 500; }
        .btn-primary:hover { background: #2563eb; }
        .btn-secondary { background: #6b7280; color: #ffffff; padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; font-weight: 500; }
        .btn-secondary:hover { background: #4b5563; }
        .grid { display: grid; grid-template-columns: repeat(1, 1fr); gap: 16px; }
        @media (min-width: 1024px) { .grid { grid-template-columns: repeat(2, 1fr); } }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; }
        .hint { font-size: 12px; color: #6b7280; }

        /* Modal básico para ayuda de marcadores */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; z-index: 50; }
        .modal-content { background: white; border-radius: 8px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); max-width: 720px; width: 92%; max-height: 90vh; overflow-y: auto; }
        .modal-header { padding: 20px 24px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; }
        .modal-body { padding: 24px; }
        .btn-close { color: #9ca3af; background: none; border: none; cursor: pointer; padding: 4px; }
        .btn-close:hover { color: #6b7280; }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="panel overflow-hidden">
                <div class="p-6 text-gray-900" x-data="{
                    showHelpModal: false,
                    exampleSubject: '¡Hemos recibido tu solicitud #@{{solicitud.id}}!',
                    exampleBody: 'Hola @{{solicitante.nombre_completo}},\nNos hemos percatado de tu solicitud sobre el servicio @{{servicio.nombre}}.\nA la brevedad, por medio de este correo, nos comunicaremos contigo.',
                    exampleFarewell: 'Saludos cordiales, Instituto Mexicano del Transporte',
                    copy(text) { if (navigator?.clipboard) { navigator.clipboard.writeText(text); } }
                }">

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-error">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-error">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <p class="hint mb-4">Configura aquí el título, contenido y despedida de los correos que se enviarán automáticamente cuando llegue una nueva solicitud. Cada destinatario puede tener un contenido diferente.</p>

                    <!-- Ayuda de marcadores -->
                    <div class="card" style="margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                            <div class="section-title">Ayuda de marcadores</div>
                            <button type="button" class="btn-secondary" @click="showHelpModal = true">Ver ejemplos y variables</button>
                        </div>
                        <p class="hint">Usa @{{...}} para insertar datos reales: por ejemplo @{{solicitante.nombre_completo}}, @{{servicio.nombre}}, @{{solicitud.id}}.</p>
                    </div>

                    <form method="POST" action="{{ route('correos.update') }}">
                        @csrf

                        <div class="grid">
                            <!-- SolicitanTe -->
                            <div class="card">
                                <div class="section-title">Para el solicitante</div>
                                <div class="mb-3">
                                    <label class="field-label">Título</label>
                                    <input type="text" class="field-input" name="subject_solicitante" value="{{ old('subject_solicitante', $templates['solicitante']['subject'] ?? '') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="field-label">Contenido</label>
                                    <textarea rows="6" class="field-input" name="body_solicitante">{{ old('body_solicitante', $templates['solicitante']['body'] ?? '') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="field-label">Despedida</label>
                                    <input type="text" class="field-input" name="farewell_solicitante" value="{{ old('farewell_solicitante', $templates['solicitante']['farewell'] ?? '') }}">
                                </div>
                                <p class="hint">Se enviará al correo del solicitante (`solicitudes_servicios.correo_electronico`).</p>
                            </div>

                            <!-- Coordinador -->
                            <div class="card">
                                <div class="section-title">Para el coordinador</div>
                                <div class="mb-3">
                                    <label class="field-label">Título</label>
                                    <input type="text" class="field-input" name="subject_coordinador" value="{{ old('subject_coordinador', $templates['coordinador']['subject'] ?? '') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="field-label">Contenido</label>
                                    <textarea rows="6" class="field-input" name="body_coordinador">{{ old('body_coordinador', $templates['coordinador']['body'] ?? '') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="field-label">Despedida</label>
                                    <input type="text" class="field-input" name="farewell_coordinador" value="{{ old('farewell_coordinador', $templates['coordinador']['farewell'] ?? '') }}">
                                </div>
                                <p class="hint">Se enviará a `coordinaciones.correo_coordinador` de la coordinación correspondiente.</p>
                            </div>

                            <!-- Asistente -->
                            <div class="card">
                                <div class="section-title">Para el asistente</div>
                                <div class="mb-3">
                                    <label class="field-label">Título</label>
                                    <input type="text" class="field-input" name="subject_asistente" value="{{ old('subject_asistente', $templates['asistente']['subject'] ?? '') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="field-label">Contenido</label>
                                    <textarea rows="6" class="field-input" name="body_asistente">{{ old('body_asistente', $templates['asistente']['body'] ?? '') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="field-label">Despedida</label>
                                    <input type="text" class="field-input" name="farewell_asistente" value="{{ old('farewell_asistente', $templates['asistente']['farewell'] ?? '') }}">
                                </div>
                                <p class="hint">Se enviará a `coordinaciones.correo_asistente` de la coordinación correspondiente.</p>
                            </div>

                            <!-- Representante -->
                            <div class="card">
                                <div class="section-title">Para el representante</div>
                                <div class="mb-3">
                                    <label class="field-label">Título</label>
                                    <input type="text" class="field-input" name="subject_representante" value="{{ old('subject_representante', $templates['representante']['subject'] ?? '') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="field-label">Contenido</label>
                                    <textarea rows="6" class="field-input" name="body_representante">{{ old('body_representante', $templates['representante']['body'] ?? '') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="field-label">Despedida</label>
                                    <input type="text" class="field-input" name="farewell_representante" value="{{ old('farewell_representante', $templates['representante']['farewell'] ?? '') }}">
                                </div>
                                <p class="hint">Se enviará a `coordinaciones.correo_representante` (global o de la coordinación) según se defina.</p>
                            </div>
                        </div>

                        <div class="mt-6" style="display: flex; justify-content: flex-end;">
                            <button type="submit" class="btn-primary">Guardar cambios</button>
                        </div>
                    </form>

                    <!-- Modal de ayuda -->
                    <div x-show="showHelpModal" x-cloak class="modal-overlay" @click.self="showHelpModal = false">
                        <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="helpModalTitle">
                            <div class="modal-header">
                                <h3 id="helpModalTitle" class="text-lg font-semibold">Marcadores disponibles y ejemplos</h3>
                                <button @click="showHelpModal = false" class="btn-close" aria-label="Cerrar">
                                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="grid" style="grid-template-columns: 1fr;">
                                    <div>
                                        <div class="section-title">Variables disponibles</div>
                                        <ul class="list-disc list-inside text-gray-700" style="margin-bottom: 12px;">
                                            <li><code>@{{ solicitud.id }}</code>, <code>@{{ solicitud.fecha_solicitud }}</code>, <code>@{{ solicitud.motivo_solicitud }}</code></li>
                                            <li><code>@{{ solicitante.nombre_completo }}</code>, <code>@{{ solicitante.correo }}</code>, <code>@{{ solicitante.telefono }}</code></li>
                                            <li><code>@{{ entidad.nombre }}</code></li>
                                            <li><code>@{{ servicio.nombre }}</code></li>
                                            <li><code>@{{ coordinacion.nombre }}</code>, <code>@{{ coordinacion.coordinador }}</code>, <code>@{{ coordinacion.correo_coordinador }}</code>, etc.</li>
                                        </ul>
                                        <p class="hint">Si algún dato no existe en la solicitud, el marcador se reemplaza por vacío.</p>
                                    </div>

                                    <div style="margin-top: 16px;">
                                        <div class="section-title">Ejemplos listos para copiar</div>
                                        <div class="card" style="margin-bottom: 12px;">
                                            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                                                <strong>Título (solicitante)</strong>
                                                <button type="button" class="btn-primary" @click="copy(exampleSubject)">Copiar</button>
                                            </div>
                                            <div class="hint" style="margin-top:6px;">@{{ solicitud.id }} inserta el folio.</div>
                                            <pre style="white-space: pre-wrap; margin-top: 8px;" x-text="exampleSubject"></pre>
                                        </div>

                                        <div class="card" style="margin-bottom: 12px;">
                                            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                                                <strong>Contenido (solicitante)</strong>
                                                <button type="button" class="btn-primary" @click="copy(exampleBody)">Copiar</button>
                                            </div>
                                            <div class="hint" style="margin-top:6px;">Se respetan los saltos de línea.</div>
                                            <pre style="white-space: pre-wrap; margin-top: 8px;" x-text="exampleBody"></pre>
                                        </div>

                                        <div class="card">
                                            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                                                <strong>Despedida</strong>
                                                <button type="button" class="btn-primary" @click="copy(exampleFarewell)">Copiar</button>
                                            </div>
                                            <pre style="white-space: pre-wrap; margin-top: 8px;" x-text="exampleFarewell"></pre>
                                        </div>
                                    </div>
                                </div>

                                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 16px;">
                                    <button type="button" class="btn-secondary" @click="showHelpModal = false">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>