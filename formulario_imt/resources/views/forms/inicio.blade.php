@extends('forms.layouts.base')

@section('title', 'PAGA IMT')

@section('content')
  <style>
    /* Responsive Design and Cross-Platform Compatibility */
    :root{ --gob-rojo:#611232; }

    /* Container responsive */
    .container-fluid { padding: 15px; }
    
    /* Navigation pills responsive */
    #pasos .nav-pills{ 
      display:flex; 
      justify-content:center; 
      align-items:stretch; 
      gap:12px; 
      padding-left:0; 
      flex-wrap:wrap; 
    }
    #pasos .nav-pills>li{ float:none; display:block; flex: 1; min-width: 200px; }
    #pasos .nav-pills>li>a{ 
      width:100%; 
      min-height:64px; 
      display:flex; 
      flex-direction:column; 
      align-items:center; 
      justify-content:center; 
      text-align:center; 
      line-height:1.2; 
      text-decoration:none; 
      color:#111; 
      background:#fff; 
      border:1px solid #ddd; 
      border-radius:4px; 
      padding:8px 14px; 
      cursor:default; 
      pointer-events:none; 
    }
    #pasos .nav-pills>li>a small{ display:block; margin-top:2px; color:#666; }
    #pasos .nav-pills>li.active>a,
    #pasos .nav-pills>li.active>a:focus,
    #pasos .nav-pills>li.active>a:hover{ background:var(--gob-rojo); color:#fff; border-color:var(--gob-rojo); }
    #pasos .nav-pills>li.active>a small{ color:#fff; }
    
    /* Button responsive */
    .btn-gob-outline{ 
      background:#fff !important; 
      color:var(--gob-rojo) !important; 
      border:2px solid var(--gob-rojo) !important; 
      box-shadow:none !important; 
      text-decoration:none !important; 
      padding: 12px 30px !important;
      font-size: 16px !important;
      min-width: 120px !important;
    }
    .btn-gob-outline:hover,
    .btn-gob-outline:focus{ 
      background:var(--gob-rojo) !important; 
      color:#fff !important; 
      border-color:var(--gob-rojo) !important; 
      box-shadow:none !important; 
      text-decoration:none !important; 
      outline: none !important; 
    }
    
    /* Form responsive */
    .form-control {
      font-size: 16px !important; /* Prevents zoom on iOS */
      padding: 12px 15px !important;
      border-radius: 4px !important;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    label {
      font-weight: 600;
      margin-bottom: 8px;
      display: block;
    }
    
    .error-message { 
      display: none; 
      color: #a94442; 
      margin-top: 5px; 
      font-size: 14px; 
    }
    
    /* Breadcrumb responsive */
    .breadcrumb {
      background: transparent;
      padding: 8px 0;
      margin-bottom: 20px;
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
    
    /* Title responsive */
    h1 {
      font-size: 3.5rem;
      margin: 20px 0 !important;
      font-weight: bold;
    }
    
    h3 {
      font-size: 2rem;
      margin-top: 0 !important;
      font-weight: bold;
    }
    
    /* Select fix - IMPORTANTE para la flecha */
    select.form-control {
      height: 50px !important;
      padding: 12px 15px !important;
      padding-right: 45px !important;
      line-height: 1.5 !important;
    }

    /* Flecha del select */
    .select-wrapper {
      position: relative;
      display: inline-block;
      width: 100%;
    }

    .select-wrapper svg {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      width: 16px;
      height: 16px;
      color: #333; /* Color de la flecha */
    }

  </style>

  {{-- Breadcrumb --}}
  <ol class="breadcrumb" style="margin-top:10px">
    <li><a href="{{ url('https://www.gob.mx/imt') }}" class="crumb-link">Inicio</a></li>
    <li>Instituto Mexicano del Transporte</li>
  </ol>

  {{-- Contenedor para la alerta --}}
  <div id="alert-placeholder" style="margin-top: 15px;"></div>

  {{-- Título --}}
  <center><h1 style="margin:10px 0 6px;">Solicitudes</h1></center>
  <br>

  {{-- Formulario --}}
  <h3 style="margin-top:0">Información:</h3>
  <div style="height:4px; width:48px; background:#a57f2c; margin:6px 0 18px;"></div>

  <form id="solicitudForm" role="form" aria-label="Formulario de solicitud" novalidate>
    {{-- Información personal --}}
    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="form-group">
          <label for="nombres">Nombre(s) <span style="color:#a00">*</span></label>
          <input type="text" id="nombres" name="nombres" class="form-control to-uppercase" placeholder="Ingresa tu nombre(s)" oninput="this.value = this.value.toUpperCase()">
          <small id="nombres-error" class="error-message">Este campo es obligatorio.</small>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="form-group">
          <label for="apellido_paterno">Apellido paterno <span style="color:#a00">*</span></label>
          <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control to-uppercase" placeholder="Ingresa tu primer apellido" oninput="this.value = this.value.toUpperCase()">
          <small id="apellido_paterno-error" class="error-message">Este campo es obligatorio.</small>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="form-group">
          <label for="apellido_materno">Apellido materno</label>
          <input type="text" id="apellido_materno" name="apellido_materno" class="form-control to-uppercase" placeholder="Ingresa tu segundo apellido" oninput="this.value = this.value.toUpperCase()">
          <small id="apellido_materno-error" class="error-message">Este campo es obligatorio.</small>
        </div>
      </div>
    </div>

    {{-- Información de contacto --}}
    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="form-group">
          <label for="razon_social">Razón social <span style="color:#a00">*</span></label>
          <input type="text" id="razon_social" name="razon_social" class="form-control to-uppercase" placeholder="Nombre de la empresa" oninput="this.value = this.value.toUpperCase()">
          <small id="razon_social-error" class="error-message">Este campo es obligatorio.</small>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="form-group">
          <label for="telefono">Teléfono <span style="color:#a00">*</span></label>
          <input type="tel" id="telefono" name="telefono" class="form-control" placeholder="Ingresa tu teléfono" maxlength="10" oninput="validatePhone()">
          <small id="telefono-error" class="error-message">Por favor, ingresa un teléfono válido de 10 dígitos.</small>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="form-group">
          <label for="correo">Correo electrónico <span style="color:#a00">*</span></label>
          <input type="email" id="correo" name="correo" class="form-control" placeholder="Ingresa tu correo electrónico" oninput="this.value = this.value.toLowerCase(); validateEmail()">
          <small id="correo-error" class="error-message">Por favor, ingresa un correo válido en minúsculas con '@'.</small>
        </div>
      </div>
    </div>

    {{-- Motivo de la solicitud --}}
    <div class="row">
      <div class="col-xs-12">
        <div class="form-group">
          <label for="motivo">Motivo de la solicitud <span style="color:#a00">*</span></label>
          <textarea id="motivo" name="motivo" class="form-control" rows="3" placeholder="Describe el motivo de tu solicitud"></textarea>
          <small id="motivo-error" class="error-message">Este campo es obligatorio.</small>
        </div>
      </div>
    </div>

    {{-- Coordinación --}}
    <div class="row">
      <div class="col-xs-12">
        <div class="form-group" style="margin-bottom: 20px;">
          <label for="coordinacion">Coordinación <span style="color:#a00">*</span></label>
          <div class="select-wrapper">
            <select class="form-control" id="coordinacion" name="coordinacion">
              <option value="" selected disabled>Selecciona la coordinación</option>
              <option value="coordinacion_desarrollo">Coordinación de Desarrollo</option>
              <option value="coordinacion_infraestructura">Coordinación de Infraestructura</option>
              <option value="coordinacion_soporte">Coordinación de Soporte</option>
              <option value="coordinacion_telematica">Coordinación de Telemática</option>
            </select>
            <!-- Ícono de flecha (Heroicons) -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </div>
          <small id="coordinacion-error" class="error-message">Este campo es obligatorio.</small>
        </div>
      </div>
    </div>
    
    {{-- Navegación --}}
    <div class="row nav-actions" style="margin-top:10px">
      <div class="col-xs-12 text-center">
        <button type="button" class="btn btn-gob-outline" id="btn-continuar" aria-label="Enviar solicitud" onclick="validateForm()">Enviar</button>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 text-left">
        <p style="margin-top:15px; color:#777; font-size:12px">* Campos obligatorios</p>
      </div>
    </div>
  </form>

  <script>
    function validatePhone() {
      const phoneInput = document.getElementById('telefono');
      const errorMessage = document.getElementById('telefono-error');
      if (phoneInput.value.length !== 10 || isNaN(phoneInput.value)) {
        errorMessage.style.display = 'block';
      } else {
        errorMessage.style.display = 'none';
      }
    }

    function validateEmail() {
      const emailInput = document.getElementById('correo');
      const errorMessage = document.getElementById('correo-error');
      const regex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/;
      if (!regex.test(emailInput.value)) {
        errorMessage.style.display = 'block';
      } else {
        errorMessage.style.display = 'none';
      }
    }

    function validateForm() {
      let valid = true;
      const requiredFields = [
        'nombres', 'apellido_paterno', 'razon_social', 'telefono', 'correo', 'motivo', 'coordinacion'
      ];

      requiredFields.forEach(field => {
        const input = document.getElementById(field);
        const errorMessage = document.getElementById(field + '-error');
        if (input.value === '') {
          errorMessage.style.display = 'block';
          valid = false;
          input.scrollIntoView({behavior: "smooth"});
        } else {
          errorMessage.style.display = 'none';
        }
      });

      if (!valid) {
        return false;
      }
      // Si todo es válido, aquí va el envío del formulario
      // document.getElementById('solicitudForm').submit(); // Descomenta esto para enviar el formulario
    }
  </script>
@endsection
