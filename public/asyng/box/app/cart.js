// Remover item del carrito

$(document).on('click', '.remove-item', function () {

  const row = $(this).closest('tr');

  row.remove();

  updateCartCount();
  updateGrandTotal();

  $('#search').val('').focus();
  saveCart();
  SoundManager.warning();

});

$(document).on('keydown', '.row-cant', function (e) {

  if (e.key === 'Enter') {

    e.preventDefault();

    $('#search').focus().select();

  }
});

$(document).on('input', '.row-cant', function () {

  const row = $(this).closest('tr');

  let cant = Number($(this).val()) || 0;

  let product = {
    price_one: parseFloat(row.find('.row-price').data('base-price')) || 0,
    price_two: parseFloat(row.find('.row-price').data('price-two')) || 0,
    cant_two: parseFloat(row.find('.row-price').data('cant-two')) || 0
  };

  let percent = getPercent();
  let price = getPrice(product, cant, percent);
  let total = cant * price;

  row.find('.row-price')
    .text(formatMoney(price))
    .data('price', price);

  row.find('.row-total')
    .text(formatMoney(total))
    .data('total', total);

  updateGrandTotal();
  saveCart();
});

$(document).on('click', '.row-cant', function () {
  $(this).select();
});

$(document).on('blur', '.row-cant', function () {

  let input = $(this); // el input que se desenfoca
  let row = input.closest('tr'); // fila correspondiente
  let value = input.val().trim();
  let cant = parseFloat(value);

  cant_row_validate(row, input, value, cant);

});

$(document).on('click', '#remove_cart', function () {

  cancelAll();
});

$(document).on('input', '#cash_payment', function () {

  // 1. Detectar si es contenteditable o input
  let raw;

  if ($(this).is('input')) {
    raw = $(this).val();
  } else {
    raw = $(this).text();
  }

  // 2. Limpiar todo lo que no sea número
  raw = raw.replace(/\D/g, '');

  // 3. Forzar el valor limpio en pantalla
  if ($(this).is('input')) {
    $(this).val(raw);
  } else {
    $(this).text(raw);
  }

  // 4. Obtener total
  let total = Number($('#grand_total').text().replace(/\./g, '')) || 0;

  // 5. Pago
  let pago = Number(raw) || 0;

  // 6. Calcular vuelto
  let vuelto = pago - total;

  // 7. Evitar negativos
  // if (vuelto < 0) vuelto = 0;

  // 8. Mostrar vuelto formateado
  setChange(vuelto);

  // Limpiar clases anteriores
  $('#change').removeClass('text-success text-danger text-dark');

  // Validar estado
  if (vuelto > 0) {
    $('#change').addClass('text-success'); // 🟢 sobra dinero
  } else if (vuelto < 0) {
    $('#change').addClass('text-danger'); // 🔴 falta dinero
  } else {
    $('#change').addClass('text-dark'); // ⚪ exacto
  }

});



$(document).on('click', '#confirm_payment', function (e) {
  sales_cash_payment();
});

