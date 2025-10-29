@extends('forms.layouts.base')

@section('title', 'PAGA IMT')

@section('content')
  <style>
    /* Variables y configuración base */
    :root {
      --gob-rojo: #611232;
      --gob-dorado: #a57f2c;
      --color-error: #a94442;
      --color-texto-secundario: #666;
    }

    /* Container responsive */
    .container-fluid { 
      padding: 15px; 
    }

    /* Breadcrumb responsive */
    .breadcrumb {
      background: transparent;
      padding: 8px 0;
      margin-bottom: 20px;
      margin-top: 10px;
    }

    .breadcrumb a {
      color: var(--gob-rojo);
      text-decoration: none;
    }

    .crumb-link,
    .crumb-link:focus,
    .crumb-link:active,
    .crumb-link:focus-visible {
      outline: none !important;
      box-shadow: none !important;
      border: 0 !important;
    }
    .crumb-link::-moz-focus-inner { border: 0; }
    .crumb-link { -webkit-tap-highlight-color: transparent; }

    /* Títulos */
    h1 {
      font-size: 3.5rem;
      margin: 10px 0 6px !important;
      font-weight: bold;
      text-align: center;
    }

    h3 {
      font-size: 2rem;
      margin-top: 0 !important;
      font-weight: bold;
    }

    .title-underline {
      height: 4px;
      width: 48px;
      background: var(--gob-dorado);
      margin: 6px 0 18px;
    }

    /* Form groups */
    .form-group {
      margin-bottom: 20px;
    }

    label {
      font-weight: 600;
      margin-bottom: 8px;
      display: block;
    }

    label .required {
      color: #a00;
    }

    /* Inputs y selects - Tamaño GRANDE */
    .form-control {
      font-size: 16px !important;
      padding: 12px 15px !important;
      border-radius: 4px !important;
      width: 100%;
      border: 1px solid #ccc;
      height: auto;
      line-height: 1.5;
      color: #333;
      background-color: #fff;
      transition: border-color 0.3s ease;
    }

    .form-control:focus {
      border-color: var(--gob-rojo);
      outline: none;
      box-shadow: 0 0 0 2px rgba(97, 18, 50, 0.1);
    }

    .form-control::placeholder {
      color: #999;
    }

    /* Textarea */
    textarea.form-control {
      resize: vertical;
      min-height: 80px;
    }

    /* Select wrapper */
    .select-wrapper {
      position: relative;
      display: inline-block;
      width: 100%;
    }

    select.form-control {
      height: 50px !important;
      padding: 12px 15px !important;
      padding-right: 45px !important;
      line-height: 1.5 !important;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      cursor: pointer;
    }

    .select-wrapper svg {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      width: 16px;
      height: 16px;
      color: #333;
      pointer-events: none;
    }

    /* Error messages */
    .error-message {
      display: none;
      color: var(--color-error);
      margin-top: 5px;
      font-size: 14px;
    }

    /* Alert styles */
    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border: 1px solid transparent;
      border-radius: 4px;
    }

    .alert-success {
      background-color: #dff0d8;
      border-color: #d6e9c6;
      color: #3c763d;
    }

    .alert-danger {
      background-color: #f2dede;
      border-color: #ebccd1;
      color: #a94442;
    }

    /* Separador horizontal */
    hr {
      border: 0;
      border-top: 1px solid #000 !important;
      margin: 20px 0;
    }

    /* Button */
    .btn-gob-outline { 
      background: #fff !important; 
      color: var(--gob-rojo) !important; 
      border: 2px solid var(--gob-rojo) !important; 
      box-shadow: none !important; 
      text-decoration: none !important; 
      padding: 12px 30px !important;
      font-size: 16px !important;
      min-width: 120px !important;
      border-radius: 4px !important;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-gob-outline:hover,
    .btn-gob-outline:focus { 
      background: var(--gob-rojo) !important; 
      color: #fff !important; 
      border-color: var(--gob-rojo) !important; 
    }

    .btn-gob-outline:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    /* Loading spinner */
    .spinner {
      border: 3px solid #f3f3f3;
      border-top: 3px solid var(--gob-rojo);
      border-radius: 50%;
      width: 20px;
      height: 20px;
      animation: spin 1s linear infinite;
      display: inline-block;
      margin-left: 10px;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Navegación */
    .nav-actions {
      margin-top: 10px;
    }

    /* Nota de campos obligatorios */
    .required-note {
      margin-top: 15px;
      color: #777;
      font-size: 12px;
    }

    /* Responsive */
    @media (max-width: 768px) {
      h1 { font-size: 2.5rem; }
      h3 { font-size: 1.5rem; }
    }

    @media (max-width: 576px) {
      h1 { font-size: 2rem; }
      h3 { font-size: 1.3rem; }
    }
  </style>

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

  <form id="solicitudForm" role="form" aria-label="Formulario de solicitud" novalidate>
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
            oninput="this.value = this.value.toLowerCase(); validateEmail()"
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
      
      <div class="col-xs-12 col-sm-6" id="entidad_otra_container" style="display:none;">
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
        </div>
      </div>
      
      <div class="col-xs-12 col-sm-6" id="servicio_otro_container" style="display:none;">
        <div class="form-group">
          <label for="servicio_otro">Especifica el servicio <span class="required">*</span></label>
          <input 
            type="text" 
            id="servicio_otro" 
            name="servicio_otro" 
            class="form-control" 
            placeholder="Describe el servicio"
          >
          <small id="servicio_otro-error" class="error-message">Este campo es obligatorio cuando seleccionas 'Otro'.</small>
        </div>
      </div>
    </div>

    {{-- Fila 5: Coordinación --}}
    <div class="row">
      <div class="col-xs-12">
        <div class="form-group">
          <label for="coordinacion">Coordinación <span class="required">*</span></label>
          <div class="select-wrapper">
            <select class="form-control" id="coordinacion" name="coordinacion">
              <option value="" selected disabled>Selecciona la coordinación</option>
              @foreach($coordinaciones as $coordinacion)
                <option value="{{ $coordinacion->id }}">{{ $coordinacion->nombre }}</option>
              @endforeach
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </div>
          <small id="coordinacion-error" class="error-message">Este campo es obligatorio.</small>
        </div>
      </div>
    </div>

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
          Enviar <span id="loading-spinner" style="display:none;" class="spinner"></span>
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
  <script>
    // Validación de teléfono
    function validatePhone() {
      const phoneInput = document.getElementById('telefono');
      const errorMessage = document.getElementById('telefono-error');
      if (phoneInput.value.length !== 10 || isNaN(phoneInput.value)) {
        errorMessage.style.display = 'block';
        return false;
      } else {
        errorMessage.style.display = 'none';
        return true;
      }
    }

    // Validación de email
    function validateEmail() {
      const emailInput = document.getElementById('correo_electronico');
      const errorMessage = document.getElementById('correo_electronico-error');
      const regex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/;
      if (!regex.test(emailInput.value)) {
        errorMessage.style.display = 'block';
        return false;
      } else {
        errorMessage.style.display = 'none';
        return true;
      }
    }

    // Manejo de "Otra entidad"
    function handleEntidadChange() {
      const entidadSelect = document.getElementById('entidad_procedencia');
      const entidadCol = document.getElementById('entidad_col');
      const container = document.getElementById('entidad_otra_container');
      const input = document.getElementById('entidad_otra');
      const error = document.getElementById('entidad_otra-error');
      
      if (entidadSelect.value === 'otra') {
        entidadCol.className = 'col-xs-12 col-sm-6';
        container.style.display = 'block';
        input.setAttribute('required', 'required');
      } else {
        entidadCol.className = 'col-xs-12';
        container.style.display = 'none';
        input.removeAttribute('required');
        input.value = '';
        error.style.display = 'none';
      }
    }

    // Manejo de "Otro servicio" y asignación automática de coordinación
    function handleServicioChange() {
      const servicioSelect = document.getElementById('servicio');
      const servicioCol = document.getElementById('servicio_col');
      const container = document.getElementById('servicio_otro_container');
      const input = document.getElementById('servicio_otro');
      const error = document.getElementById('servicio_otro-error');
      const coordinacionSelect = document.getElementById('coordinacion');
      
      if (servicioSelect.value === 'otro') {
        servicioCol.className = 'col-xs-12 col-sm-6';
        container.style.display = 'block';
        input.setAttribute('required', 'required');
      } else {
        servicioCol.className = 'col-xs-12';
        container.style.display = 'none';
        input.removeAttribute('required');
        input.value = '';
        error.style.display = 'none';
        
        // Asignar coordinación predeterminada automáticamente
        const selectedOption = servicioSelect.options[servicioSelect.selectedIndex];
        const coordinacionId = selectedOption.getAttribute('data-coordinacion');
        if (coordinacionId) {
          coordinacionSelect.value = coordinacionId;
        }
      }
    }

    // Envío del formulario con AJAX
    $(document).ready(function() {
      $('#solicitudForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validar formulario
        if (!validarFormulario()) {
          return false;
        }
        
        // Deshabilitar botón y mostrar spinner
        const btnEnviar = $('#btn-enviar');
        const spinner = $('#loading-spinner');
        btnEnviar.prop('disabled', true);
        spinner.show();
        
        // Obtener datos del formulario
        const formData = $(this).serialize();
        
        // Enviar con AJAX
        $.ajax({
          url: '{{ route("solicitud.store") }}',
          method: 'POST',
          data: formData,
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              // Mostrar mensaje de éxito
              $('#alert-placeholder').html(`
                <div class="alert alert-success" role="alert">
                  <strong>¡Solicitud enviada correctamente!</strong><br>
                  ${response.message}<br>
                  Gracias por completar el formulario. El Instituto Mexicano del Transporte revisará su solicitud y le contactará a la brevedad al correo proporcionado.
                </div>
              `);
              
              // Limpiar formulario
              $('#solicitudForm')[0].reset();
              
              // Scroll al inicio
              window.scrollTo({ top: 0, behavior: 'smooth' });
            }
          },
          error: function(xhr) {
            let errorMessage = 'Error al enviar la solicitud. Por favor, intente nuevamente.';
            
            if (xhr.responseJSON && xhr.responseJSON.errors) {
              errorMessage = '<ul>';
              $.each(xhr.responseJSON.errors, function(field, messages) {
                messages.forEach(function(message) {
                  errorMessage += '<li>' + message + '</li>';
                });
              });
              errorMessage += '</ul>';
            }
            
            $('#alert-placeholder').html(`
              <div class="alert alert-danger" role="alert">
                <strong>Error:</strong> ${errorMessage}
              </div>
            `);
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
          },
          complete: function() {
            btnEnviar.prop('disabled', false);
            spinner.hide();
          }
        });
      });
    });

    // Validación completa del formulario
    function validarFormulario() {
      let valid = true;
      const requiredFields = [
        'nombres', 'apellido_paterno', 'telefono', 'correo_electronico', 
        'motivo_solicitud', 'coordinacion', 'entidad_procedencia', 'servicio'
      ];

      requiredFields.forEach(field => {
        const input = document.getElementById(field);
        const errorMessage = document.getElementById(field + '-error');
        if (!input.value.trim()) {
          errorMessage.style.display = 'block';
          valid = false;
          if (valid === false) input.scrollIntoView({behavior: "smooth", block: "center"});
        } else {
          errorMessage.style.display = 'none';
        }
      });

      // Validar entidad "Otra"
      const entidadSelect = document.getElementById('entidad_procedencia');
      if (entidadSelect.value === 'otra') {
        const entidadOtra = document.getElementById('entidad_otra');
        const entidadOtraError = document.getElementById('entidad_otra-error');
        if (!entidadOtra.value.trim()) {
          entidadOtraError.style.display = 'block';
          valid = false;
        } else {
          entidadOtraError.style.display = 'none';
        }
      }

      // Validar servicio "Otro"
      const servicioSelect = document.getElementById('servicio');
      if (servicioSelect.value === 'otro') {
        const servicioOtro = document.getElementById('servicio_otro');
        const servicioOtroError = document.getElementById('servicio_otro-error');
        if (!servicioOtro.value.trim()) {
          servicioOtroError.style.display = 'block';
          valid = false;
        } else {
          servicioOtroError.style.display = 'none';
        }
      }

      // Validar teléfono y email
      if (!validatePhone()) valid = false;
      if (!validateEmail()) valid = false;

      return valid;
    }
  </script>
@endsection