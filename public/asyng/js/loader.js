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
    digitsOptional: false, // siempre muestra un decimal (00.0)
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
// Alert Global
function showGlobalAlert(message, type = 'warning') {

  const alertBox = $('#globalFormAlert');

  alertBox
    .removeClass('alert-warning alert-danger alert-success alert-info')
    .addClass('alert-' + type);

  alertBox.find('.alert-message').text(message);

  alertBox.addClass('show');

  // Scroll automático (recomendado)
  $('html, body').animate({
    scrollTop: alertBox.offset().top - 20
  }, 300);
}
function hideGlobalAlert() {
  $('#globalFormAlert').removeClass('show');
}

// Validacion de Formulario
function validateForm(formSelector) {
  let valid = true;

  $(formSelector + ' [required]').each(function () {
    if ($.trim($(this).val()) === '') {
      $(this).addClass('is-invalid');
      valid = false;
    } else {
      $(this).removeClass('is-invalid');
    }
  });

  if (!valid) {
    showGlobalAlert('Complete los campos obligatorios.', 'warning');
  }

  return valid;
}

