// Fallback del módulo ES6 para la vista Solicitud de Servicios IMT
// Igual que resources/js/servicios.js, sin dependencias de bundler

export function validatePhone() {
  const phoneInput = document.getElementById('telefono');
  const errorMessage = document.getElementById('telefono-error');
  if (!phoneInput) return true;
  if (phoneInput.value.length !== 10 || isNaN(phoneInput.value)) {
    errorMessage && (errorMessage.style.display = 'block');
    return false;
  } else {
    errorMessage && (errorMessage.style.display = 'none');
    return true;
  }
}

export function validateEmail() {
  const emailInput = document.getElementById('correo_electronico');
  const errorMessage = document.getElementById('correo_electronico-error');
  const regex = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
  if (!emailInput) return true;
  if (!regex.test(emailInput.value)) {
    errorMessage && (errorMessage.style.display = 'block');
    return false;
  } else {
    errorMessage && (errorMessage.style.display = 'none');
    return true;
  }
}

export function handleEntidadChange() {
  const entidadSelect = document.getElementById('entidad_procedencia');
  const container = document.getElementById('entidad_otra_container');
  const input = document.getElementById('entidad_otra');
  const error = document.getElementById('entidad_otra-error');

  if (!entidadSelect || !container || !input) return;

  if (entidadSelect.value === 'otra') {
    container.style.display = 'block';
    input.setAttribute('required', 'required');
  } else {
    container.style.display = 'none';
    input.removeAttribute('required');
    input.value = '';
    error && (error.style.display = 'none');
  }
}

export function handleServicioChange() {
  const servicioSelect = document.getElementById('servicio');
  const container = document.getElementById('servicio_otro_container');
  const input = document.getElementById('servicio_otro');
  const error = document.getElementById('servicio_otro-error');
  const coordinacionHidden = document.getElementById('coordinacion');

  if (!servicioSelect || !container || !input || !coordinacionHidden) return;

  if (servicioSelect.value === 'otro') {
    container.style.display = 'block';
    input.setAttribute('required', 'required');
    coordinacionHidden.value = '';
    coordinacionHidden.setAttribute('disabled', 'disabled');
    const coordErr = document.getElementById('coordinacion-error');
    if (coordErr) coordErr.style.display = 'none';
  } else {
    container.style.display = 'none';
    input.removeAttribute('required');
    input.value = '';
    error && (error.style.display = 'none');

    const selectedOption = servicioSelect.options[servicioSelect.selectedIndex];
    const coordinacionId = selectedOption.getAttribute('data-coordinacion');
    coordinacionHidden.removeAttribute('disabled');
    coordinacionHidden.value = coordinacionId || '';

    const coordErr = document.getElementById('coordinacion-error');
    if (coordErr) coordErr.style.display = (!coordinacionId || coordinacionId === 'null') ? 'block' : 'none';
  }
}

export function validarFormulario() {
  let valid = true;
  const requiredFields = [
    'nombres', 'apellido_paterno', 'telefono', 'correo_electronico', 
    'motivo_solicitud', 'entidad_procedencia', 'servicio'
  ];

  requiredFields.forEach(field => {
    const input = document.getElementById(field);
    const errorMessage = document.getElementById(field + '-error');
    if (!input) return;
    if (!input.value.trim()) {
      errorMessage && (errorMessage.style.display = 'block');
      valid = false;
      if (valid === false) input.scrollIntoView({behavior: 'smooth', block: 'center'});
    } else {
      errorMessage && (errorMessage.style.display = 'none');
    }
  });

  const entidadSelect = document.getElementById('entidad_procedencia');
  if (entidadSelect && entidadSelect.value === 'otra') {
    const entidadOtra = document.getElementById('entidad_otra');
    const entidadOtraError = document.getElementById('entidad_otra-error');
    if (entidadOtra && !entidadOtra.value.trim()) {
      entidadOtraError && (entidadOtraError.style.display = 'block');
      valid = false;
    } else {
      entidadOtraError && (entidadOtraError.style.display = 'none');
    }
  }

  const servicioSelect = document.getElementById('servicio');
  if (servicioSelect && servicioSelect.value === 'otro') {
    const servicioOtro = document.getElementById('servicio_otro');
    const servicioOtroError = document.getElementById('servicio_otro-error');
    if (servicioOtro && !servicioOtro.value.trim()) {
      servicioOtroError && (servicioOtroError.style.display = 'block');
      valid = false;
    } else {
      servicioOtroError && (servicioOtroError.style.display = 'none');
    }
  }

  if (servicioSelect && servicioSelect.value && servicioSelect.value !== 'otro') {
    const coordinacionHidden = document.getElementById('coordinacion');
    const coordErr = document.getElementById('coordinacion-error');
    const valor = coordinacionHidden ? coordinacionHidden.value : '';
    if (!valor || valor === 'null') {
      if (coordErr) coordErr.style.display = 'block';
      valid = false;
    } else {
      if (coordErr) coordErr.style.display = 'none';
    }
  }

  if (!validatePhone()) valid = false;
  if (!validateEmail()) valid = false;

  return valid;
}

export function initServicios() {
  if (!window.jQuery) {
    console.warn('[servicios.js] jQuery no está disponible; la vista requiere jQuery CDN.');
  }

  window.jQuery && window.jQuery(function($) {
    $('#solicitudForm').on('submit', function(e) {
      e.preventDefault();
      if (!validarFormulario()) {
        return false;
      }
      const btnEnviar = $('#btn-enviar');
      const spinner = $('#loading-spinner');
      const btnText = $('#btn-text');
      const initialWidth = btnEnviar.outerWidth();
      btnEnviar.css('width', initialWidth + 'px');
      btnEnviar.prop('disabled', true);
      btnText.hide();
      spinner.show().removeClass('with-text');
      const formData = $(this).serialize();
      const actionUrl = (document.getElementById('solicitudForm')?.dataset?.action) || (window.route_solicitud_store || '');
      $.ajax({
        url: actionUrl,
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            $('#alert-placeholder').html(
              '<div class="alert alert-success" role="alert">' +
              '<strong>¡Solicitud enviada correctamente!</strong><br>' +
              (response.message || '') + '<br>' +
              '</div>'
            );
            $('#solicitudForm')[0].reset();
            window.scrollTo({ top: 0, behavior: 'smooth' });
          }
        },
        error: function(xhr) {
          let errorMessage = 'Error al enviar la solicitud. Por favor, intente nuevamente.';
          if (xhr.responseJSON && xhr.responseJSON.errors) {
            errorMessage = '<ul>';
            window.jQuery.each(xhr.responseJSON.errors, function(field, messages) {
              messages.forEach(function(message) {
                errorMessage += '<li>' + message + '</li>';
              });
            });
            errorMessage += '</ul>';
          }
          $('#alert-placeholder').html(
            '<div class="alert alert-danger" role="alert">' +
            '<strong>Error:</strong> ' + errorMessage +
            '</div>'
          );
          window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        complete: function() {
          btnEnviar.prop('disabled', false);
          spinner.hide().addClass('with-text');
          $('#btn-text').show();
          btnEnviar.css('width', '');
        }
      });
    });
  });

  const entidadSelect = document.getElementById('entidad_procedencia');
  const servicioSelect = document.getElementById('servicio');
  if (entidadSelect) entidadSelect.addEventListener('change', handleEntidadChange);
  if (servicioSelect) servicioSelect.addEventListener('change', handleServicioChange);
}

document.addEventListener('DOMContentLoaded', initServicios);

// Exponer funciones globalmente para compatibilidad con atributos inline existentes
window.validatePhone = validatePhone;
window.validateEmail = validateEmail;
window.handleEntidadChange = handleEntidadChange;
window.handleServicioChange = handleServicioChange;
window.validarFormulario = validarFormulario;