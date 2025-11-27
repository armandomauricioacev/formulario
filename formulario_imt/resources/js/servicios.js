// Módulo ES6 para la vista Solicitud de Servicios IMT
// Mantiene funcionalidad original y depende de jQuery global (CDN)

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
  // Dependencia: jQuery debe estar disponible globalmente
  if (!window.jQuery) {
    console.warn('[servicios.js] jQuery no está disponible; la vista requiere jQuery CDN.');
  }

  // Envío del formulario con AJAX
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
            // Oculta panel debug si estaba visible
            if (window.APP_DEBUG) {
              document.getElementById('debug-section')?.setAttribute('style', 'display:none;');
              document.getElementById('error-details') && (document.getElementById('error-details').textContent = '');
              document.getElementById('debug-logs') && (document.getElementById('debug-logs').textContent = '');
            }
          }
        },
        error: function(xhr) {
          let html = '';
          // Construir detalles ricos de error
          if (xhr.responseJSON) {
            const json = xhr.responseJSON;
            if (json.errors) {
              html += '<ul>';
              window.jQuery.each(json.errors, function(field, messages) {
                messages.forEach(function(message) { html += '<li>' + message + '</li>'; });
              });
              html += '</ul>';
            }
            if (json.message) {
              html += '<div><strong>Mensaje:</strong> ' + window.jQuery.escapeSelector ? json.message : json.message + '</div>';
            }
            if (json.exception) {
              html += '<div><strong>Excepción:</strong> ' + json.exception + '</div>';
            }
            if (json.file) {
              html += '<div><strong>Archivo:</strong> ' + json.file + ':' + (json.line || '') + '</div>';
            }
          } else {
            // Fallback: texto de respuesta o status
            const text = (xhr.responseText || '').trim();
            html += '<div><strong>Status:</strong> ' + xhr.status + ' ' + (xhr.statusText || '') + '</div>';
            if (text) {
              // Mostrar un extracto si es HTML largo
              const snippet = text.length > 2000 ? text.substring(0, 2000) + '... (truncado)' : text;
              html += '<pre style="white-space: pre-wrap; max-height: 300px; overflow:auto;">' + window.jQuery('<div/>').text(snippet).html() + '</pre>';
            }
          }
          $('#alert-placeholder').html(
            '<div class="alert alert-danger" role="alert">' +
            '<strong>Error:</strong><br>' + html +
            '</div>'
          );
          window.scrollTo({ top: 0, behavior: 'smooth' });

          // Si está habilitado el modo debug, mostrar panel y cargar logs
          if (window.APP_DEBUG) {
            const section = document.getElementById('debug-section');
            const details = document.getElementById('error-details');
            const logs = document.getElementById('debug-logs');
            if (section) {
              section.style.display = 'block';
            }
            if (details) {
              // Mostrar más metadatos del XHR
              const meta = {
                url: actionUrl,
                status: xhr.status,
                statusText: xhr.statusText,
                responseType: xhr.responseType,
                readyState: xhr.readyState,
              };
              details.textContent = JSON.stringify(meta, null, 2);
            }
            if (logs) {
              fetch('/debug/logs', { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                .then(r => r.text())
                .then(text => { logs.textContent = text; })
                .catch(err => { logs.textContent = 'No se pudo cargar logs: ' + (err && err.message ? err.message : String(err)); });
            }
            const btnReload = document.getElementById('btn-recargar-logs');
            if (btnReload) {
              btnReload.onclick = function(){
                fetch('/debug/logs', { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                  .then(r => r.text())
                  .then(text => { document.getElementById('debug-logs').textContent = text; })
                  .catch(err => { document.getElementById('debug-logs').textContent = 'No se pudo cargar logs: ' + (err && err.message ? err.message : String(err)); });
              };
            }
          }
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

  // Asociar handlers de cambios
  const entidadSelect = document.getElementById('entidad_procedencia');
  const servicioSelect = document.getElementById('servicio');
  if (entidadSelect) entidadSelect.addEventListener('change', handleEntidadChange);
  if (servicioSelect) servicioSelect.addEventListener('change', handleServicioChange);
}

// Auto-inicialización al cargar el módulo
document.addEventListener('DOMContentLoaded', initServicios);

// Exponer funciones globalmente para compatibilidad con atributos inline existentes
window.validatePhone = validatePhone;
window.validateEmail = validateEmail;
window.handleEntidadChange = handleEntidadChange;
window.handleServicioChange = handleServicioChange;
window.validarFormulario = validarFormulario;
