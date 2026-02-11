const CSRF = {
  name: csrfName,
  hash: csrfHash
};
// Estilizar input para mostrar monedas 000.000
function asyngMoneyMask(selector = ".money") {
  $(selector).inputmask({
    alias: "numeric",
    groupSeparator: ".",
    autoGroup: true,
    digits: 0,
    rightAlign: false,
    removeMaskOnSubmit: true
  });
}
// Estilizar input para mostrar solo numeros
function asyngNumericMask(selector = ".numeric") {
  $(selector).inputmask({
    alias: "numeric",
    groupSeparator: "",
    autoGroup: false,
    digits: 2,
    rightAlign: false,
    allowMinus: false,
    placeholder: "",
    removeMaskOnSubmit: true
  });
}
// Estilizar input para mostrar stock
function asyngNumericStock(selector = ".stock") {
  $(selector).inputmask({
    alias: "numeric",
    groupSeparator: "",
    autoGroup: false,
    digits: 3,
    rightAlign: false,
    allowMinus: true,
    placeholder: "",
    removeMaskOnSubmit: true,
    digitsOptional: true,
    radixPoint: ".",
    allowPlus: false
  });
}
// Estilizar input para mostrar porcentaje
function asyngPercentMask(selector = ".percent") {
  $(selector).inputmask({
    alias: "numeric",
    suffix: " %",
    groupSeparator: "",
    autoGroup: false,
    digits: 1,
    digitsOptional: true,
    rightAlign: false,
    allowMinus: false,
    placeholder: "",
    removeMaskOnSubmit: true,
    onBeforeMask: function (value) {
      return value ? value.toString().replace('%', '').trim() : value;
    }
  });
}
/* Estilos a inputs, efectos visuales */
function asyngVisualEffects($elements) {
  $elements.forEach($el => {
    $el.css({
      "border": "2px solid #28a745",
      "transition": "border 0.3s ease"
    });
    setTimeout(() => {
      $el.css("border", "");
    }, 1000);
  });
}

/* Mayusculas a input */
$(document).on('input', 'input[type="text"]:not(.money):not(.percent):not(.numeric):not(.stock)', function () {
  this.value = this.value.toUpperCase();
});


/* Boton Cancealr de Formulario */
function open_hide(open, hide, html, callback = null) {
  $(hide).fadeOut(200, function () {
    $(open).html(html).slideDown(200, function () {
      if (typeof callback === 'function') callback();
    });
  });
}

function cancel_open(hide, panelToOpen) {
  $(hide).fadeOut(200, function () {
    $(panelToOpen).slideDown(200);
  });
}

// Validacion de Formulario
function validateForm(formSelector) {
  let valid = true;

  $(formSelector + ' [required]').each(function () {
    if ($.trim($(this).val()) === '') {
      $(this).addClass('is-invalid');
      setTimeout(function () {
        $(this).removeClass('is-invalid');
      }, 10000);
      valid = false;
    } else {
      $(this).removeClass('is-invalid');
    }
  });

  if (!valid) {
    showAlert('Complete los campos obligatorios.', 'warning');
  }

  return valid;
}

/**
 * Convierte texto con separadores a número
 */
function toNumber(value) {
  if (!value) return 0;
  return parseFloat(value.replace(/\./g, '').replace(',', '.')) || 0;
}

/**
 * Formatea número sin decimales (igual que money)
 */
function formatMoney(value) {
  return new Intl.NumberFormat('es-ES', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value);
}
function toMoneyNumber(value) {
  if (!value) return 0;

  return parseFloat(
    value
      .toString()
      .replace(/\./g, '')   // quita separador de miles
      .replace(',', '.')   // por si algún decimal
  ) || 0;
}
function toPercentNumber(value) {
  if (!value) return 0;

  return parseFloat(
    value
      .toString()
      .replace('%', '')
      .replace(',', '.')
      .trim()
  ) || 0;
}

// FUNCIÓN GLOBAL PARA ALERTAS
function showAlert(message, type = 'warning', timeout = 4000) {

  const $container = $('#global-alert-container');

  // Limpiar alertas previas
  $container.empty();

  const $alert = $(`
    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
      <strong>${message}</strong>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
      </button>
    </div>
  `);

  // Insertar alert
  $container.append($alert);

  // Auto cerrar
  if (timeout) {
    setTimeout(() => {
      $alert.alert('close');
    }, timeout);
  }
}

function asyng_show_view({
  id,
  html,
  effect = 'fade',
  time = 150
}) {

  let $div = $('#' + id);

  if (effect === 'slide') {

    $div.slideUp(time, function () {
      $div.html(html).slideDown(time);
    });

  } else {

    $div.fadeOut(time, function () {
      $div.html(html).fadeIn(time);
    });

  }

}

function asyng_hide_view({
  id,
  effect = 'fade',
  time = 150,
  clear = false   // limpiar contenido al cerrar
}) {

  let $div = $('#' + id);

  if (!$div.length) return;

  if (effect === 'slide') {

    $div.slideUp(time, function () {

      if (clear) $div.html('');

    });

  } else if (effect === 'scale') {

    $div.animate(
      { opacity: 0 },
      {
        duration: time,
        step: function () {
          $(this).css('transform', 'scale(0.98)');
        },
        complete: function () {

          $(this).hide();

          if (clear) $(this).html('');

        }
      }
    );

  } else {

    // fade default
    $div.fadeOut(time, function () {

      if (clear) $div.html('');

    });

  }

}
