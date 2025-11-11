@extends('forms.layouts.base')

@section('title', 'Servicios IMT')

@section('content')
  {{-- Assets dedicados de la vista (con fallback si no existe manifest de Vite) --}}
  @if (file_exists(public_path('build/manifest.json')))
    @vite(['resources/css/servicios.css'])
    @vite(['resources/js/servicios.js'])
  @else
    <link rel="stylesheet" href="{{ asset('css/servicios.css') }}">
    <script type="module" src="{{ asset('js/servicios.js') }}" defer></script>
  @endif


  {{-- Breadcrumb --}}
  <ol class="breadcrumb">
    <li><a href="{{ url('https://www.gob.mx/imt') }}" class="crumb-link">Inicio</a></li>
    <li>Instituto Mexicano del Transporte</li>
  </ol>

  {{-- Contenedor para alertas --}}
  <div id="alert-placeholder" style="margin-top: 15px;"></div>

  {{-- Título principal --}}
  <center><h2>Solicitud de servicios IMT</h2></center>
  <br>

  {{-- Formulario --}}
  <h3>Información:</h3>
  <div class="title-underline"></div>

  <form id="solicitudForm" role="form" aria-label="Formulario de solicitud" novalidate data-action="{{ route('solicitud.store') }}">
    @csrf
    
    {{-- Fila 1: Datos personales --}}
    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="form-group">
          <label for="nombres">Nombre(s) <span class="required">*</span></label>
          <input 
            type="text" 
            id="nombres" 
            name="nombres" 
            class="form-control to-uppercase" 
            placeholder="Ingresa tu nombre(s)" 
            oninput="this.value = this.value.toUpperCase()"
            autocomplete="given-name"
          >
          <small id="nombres-error" class="error-message">Este campo es obligatorio.</small>
        </div>
      </div>
      
      <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="form-group">
          <label for="apellido_paterno">Apellido paterno <span class="required">*</span></label>
          <input 
            type="text" 
            id="apellido_paterno" 
            name="apellido_paterno" 
            class="form-control to-uppercase" 
            placeholder="Ingresa tu primer apellido" 
            oninput="this.value = this.value.toUpperCase()"
            autocomplete="family-name"
          >
          <small id="apellido_paterno-error" class="error-message">Este campo es obligatorio.</small>
        </div>
      </div>
      
      <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="form-group">
          <label for="apellido_materno">Apellido materno</label>
          <input 
            type="text" 
            id="apellido_materno" 
            name="apellido_materno" 
            class="form-control to-uppercase" 
            placeholder="Ingresa tu segundo apellido" 
            oninput="this.value = this.value.toUpperCase()"
            autocomplete="additional-name"
          >
        </div>
      </div>
    </div>

    {{-- Fila 2: Teléfono y Correo --}}
    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-4 col-md-offset-2">
        <div class="form-group">
          <label for="telefono">Teléfono <span class="required">*</span></label>
          <input 
            type="tel" 
            id="telefono" 
            name="telefono" 
            class="form-control" 
            placeholder="Ingresa tu número de teléfono" 
            maxlength="10"
            inputmode="numeric"
            pattern="[0-9]*"
            autocomplete="tel"
            oninput="this.value = this.value.replace(/[^0-9]/g, ''); validatePhone()"
          >
          <small id="telefono-error" class="error-message">Por favor, ingresa un teléfono válido de 10 dígitos.</small>
        </div>
      </div>
      
      <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="form-group">
          <label for="correo_electronico">Correo electrónico <span class="required">*</span></label>
          <input 
            type="email" 
            id="correo_electronico" 
            name="correo_electronico" 
            class="form-control" 
            placeholder="Ingresa tu correo electrónico"
            autocomplete="email"
            oninput="validateEmail()"
          >
          <small id="correo_electronico-error" class="error-message">Por favor, ingresa un correo válido.</small>
        </div>
      </div>
    </div>

    <hr>

    {{-- Fila 3: Entidad de procedencia --}}
    <div class="row">
      <div class="col-xs-12" id="entidad_col">
        <div class="form-group">
          <label for="entidad_procedencia">Entidad de procedencia <span class="required">*</span></label>
          <div class="select-wrapper">
            <select class="form-control" id="entidad_procedencia" name="entidad_procedencia" onchange="handleEntidadChange()">
              <option value="" selected disabled>Selecciona la entidad</option>
              @foreach($entidades as $entidad)
                <option value="{{ $entidad->id }}">{{ $entidad->nombre }}</option>
              @endforeach
              <option value="otra">Otra</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </div>
          <small id="entidad_procedencia-error" class="error-message">Este campo es obligatorio.</small>
        </div>
      </div>
      
      <div class="col-xs-12" id="entidad_otra_container" style="display:none;">
        <div class="form-group">
          <label for="entidad_otra">Especifica la entidad <span class="required">*</span></label>
          <input 
            type="text" 
            id="entidad_otra" 
            name="entidad_otra" 
            class="form-control to-uppercase" 
            placeholder="Nombre de la entidad"
            oninput="this.value = this.value.toUpperCase()"
          >
          <small id="entidad_otra-error" class="error-message">Este campo es obligatorio cuando seleccionas 'Otra'.</small>
        </div>
      </div>
    </div>

    {{-- Fila 4: Servicio --}}
    <div class="row">
      <div class="col-xs-12" id="servicio_col">
        <div class="form-group">
          <label for="servicio">Selecciona el servicio <span class="required">*</span></label>
          <div class="select-wrapper">
            <select class="form-control" id="servicio" name="servicio" onchange="handleServicioChange()">
              <option value="" selected disabled>Selecciona el servicio</option>
              @foreach($servicios as $servicio)
                <option value="{{ $servicio->id }}" data-coordinacion="{{ $servicio->coordinacion_predeterminada_id }}">
                  {{ $servicio->nombre }}
                </option>
              @endforeach
              <option value="otro">Otro</option>
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </div>
          <small id="servicio-error" class="error-message">Este campo es obligatorio.</small>
          <small id="coordinacion-error" class="error-message">El servicio seleccionado no tiene coordinación predeterminada. Seleccione otro servicio o elija "Otro".</small>
        </div>
      </div>
      
      <div class="col-xs-12" id="servicio_otro_container" style="display:none;">
        <div class="form-group">
          <label for="servicio_otro">Especifica el servicio <span class="required">*</span></label>
          <input 
            type="text" 
            id="servicio_otro" 
            name="servicio_otro" 
            class="form-control to-uppercase" 
            placeholder="Describe el servicio"
            oninput="this.value = this.value.toUpperCase()"
          >
          <small id="servicio_otro-error" class="error-message">Este campo es obligatorio cuando seleccionas 'Otro'.</small>
        </div>
      </div>
    </div>

    {{-- Coordinación oculta para envío automático --}}
    <input type="hidden" id="coordinacion" name="coordinacion" value="">

    {{-- Fila 6: Motivo --}}
    <div class="row">
      <div class="col-xs-12">
        <div class="form-group">
          <label for="motivo_solicitud">Motivo de la solicitud <span class="required">*</span></label>
          <textarea 
            id="motivo_solicitud" 
            name="motivo_solicitud" 
            class="form-control" 
            rows="3" 
            placeholder="Describe el motivo de tu solicitud"
          ></textarea>
          <small id="motivo_solicitud-error" class="error-message">Este campo es obligatorio.</small>
        </div>
      </div>
    </div>
    
    {{-- Navegación --}}
    <div class="row nav-actions">
      <div class="col-xs-12 text-center">
        <button 
          type="submit" 
          class="btn btn-gob-outline" 
          id="btn-enviar" 
          aria-label="Enviar solicitud"
        >
          <span id="btn-text">Enviar</span>
          <span id="loading-spinner" style="display:none;" class="spinner with-text" aria-live="polite" aria-label="Cargando"></span>
        </button>
      </div>
    </div>

    {{-- Nota de campos obligatorios --}}
    <div class="row">
      <div class="col-xs-12 text-left">
        <p class="required-note">* Campos obligatorios</p>
      </div>
    </div>
  </form>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection