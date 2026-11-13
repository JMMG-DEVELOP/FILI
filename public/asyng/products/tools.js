async function window_view(route, div, data = {}, callback = null) {

  try {

    const response = await asyngAjaxSend(
      route,
      data
    );

    if (!response || response.status !== true) {

      showAlert(
        response?.msg ||
        response?.error ||
        'ERROR AL CARGAR',
        'warning'
      );

      return false;
    }

    asyng_show_view({

      id: div,
      html: response.html,
      effect: 'fade',

      callback: function () {

        // Ejecutar inicialización adicional si existe
        if (typeof callback === 'function') {
          callback(response);
        }

      }

    });

    return response;

  } catch (error) {

    console.error(
      'ERROR AL CARGAR DETALLES:',
      error
    );

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );

    return false;
  }
}

function formatGuaranies(value) {

  if (value === null || value === undefined || value === '') {
    return '0';
  }

  let number = String(value).replace(/\D/g, '');

  number = number.replace(/^0+(?=\d)/, '');

  return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
function formatGuaraniesInputs(container) {

  $(container).find('.guaranies').each(function () {

    const value = $(this).val();

    $(this).val(formatGuaranies(value));

  });

}
// FORMATEAR CÓDIGO A 7 DÍGITOS
function formatCode(input) {

  let raw = $(input).val().trim();

  // Eliminar ceros a la izquierda
  raw = raw.replace(/^0+/, '');

  // Evitar vacío
  if (raw === '') {
    raw = '0';
  }

  // Rellenar hasta 7 dígitos
  const formatted = raw.length <= 7
    ? raw.padStart(7, '0')
    : raw;

  $(input).val(formatted);
}