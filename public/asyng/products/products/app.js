

$(document).on('click', '.product_cancel', function (e) {
  e.preventDefault();
  cancel_open('#products_form', '#products_panel');
});

$(document).on('keydown', '.money[name^="price_"]', function (e) {

  if (e.key === 'Enter') {
    e.preventDefault();
    calculateFromPrice($(this));
  }
});

$(document).on('keydown', '.money[name^="margin_"]', function (e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    calculateFromMargin($(this));
  }
});

$(document).on('keydown', '.percent[name^="x"]', function (e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    calculateFromPercent($(this));
  }
});
$(document).on('keydown', '[name="cost"]', function (e) {

  if (e.key === 'Enter') {
    e.preventDefault();
    recalculateFromCost();
  }

});

// FORMATEO DE INPUT CODE
$(document).on('input', '#code', function () {

  let raw = $(this).val().trim();

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

  // 🔑 Asignar al input
  $(this).val(formatted);
});

$(document).on('keydown', '#code', async function (e) {

  if (e.key === 'Enter') {
    e.preventDefault();
    let data = {
      code: $('#code').val()
    };

    code_verify(data);

  }

});

