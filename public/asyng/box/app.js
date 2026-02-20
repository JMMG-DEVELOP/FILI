$(document).on('keydown', '#search', async function (e) {

  let value = $(this).val().trim();

  let data = {
    value: value
  };

  /**
   * SHIFT → abrir buscador modal
   */
  if (e.key === 'Shift' && !e.repeat) {

    e.preventDefault();
    if (!value) {

      asyng_hide_view({
        id: 'search_panel',
        effect: 'fade',
        clear: true
      });

      return;
    }

    product_search(data);
    return;
  }

  /**
   * ENTER → buscar / validar
   */
  if (e.key === 'Enter' && !e.repeat) {

    e.preventDefault();

    if (!value) {

      showAlert('Campo Vacío', 'warning');

      asyng_hide_view({
        id: 'search_panel',
        effect: 'fade',
        clear: true
      });

      SoundManager.error();
      return;
    }

    const formatted = value.length <= 7
      ? value.padStart(7, '0')
      : value;
    $(this).val(formatted);

    invoice_add_product_card(formatted)

  }

});

$(document).on('click', '.product_search_cancel', function () {

  asyng_hide_view({
    id: 'search_panel',
    effect: 'fade',
    clear: true
  });
  $("#search").focus();

  return;
});
$(document).on('keydown', '#product_cant', async function (e) {
  if (e.key === 'Enter' && !e.repeat) {
    $('#search').focus();
  }
});
$(document).on('keydown', '#add_card', async function (e) {
  if (e.key === 'Enter' && !e.repeat) {
    $('#search').focus();
  }
});
$(document).on('keydown', function (e) {

  // CTRL + P → Tipo de Pago
  if (e.ctrlKey && e.key.toLowerCase() === 'p') {
    e.preventDefault(); // evita imprimir (print)

    $('#payment').focus().select2('open');

  }

  // CTRL + B → Tipo de Venta
  if (e.ctrlKey && e.key.toLowerCase() === 'b') {
    e.preventDefault(); // evita pegar (paste)

    $('#sales').focus().select2('open');
  }

  // CTRL + x → Cerrar Busquedas
  if (e.ctrlKey && e.key.toLowerCase() === 'x') {
    e.preventDefault(); // evita pegar (paste)
    asyng_hide_view({
      id: 'search_panel',
      effect: 'fade',
      clear: true
    });
    $("#search").focus();

    return;
  }

  // CTRL + T → Focus en Cantidad
  if (e.ctrlKey && e.key.toLowerCase() === 'y') {
    e.preventDefault();

    $('#product_cant').focus();
    $('#product_cant').select();
  }

  // CTRL + A → fOCUS EN TIPO DE ADICION A CARRITO
  if (e.ctrlKey && e.key.toLowerCase() === 'a') {
    e.preventDefault();

    $('#add_card').focus();
  }

  // Presionar Escape y Cerrar Venta
  if (e.key === 'Escape') {
    e.preventDefault();

    SoundManager.payment();
    // alert('Cerrar Venta');
  }

});
$(document).on('input', '.row-cant', function () {
  handleQuantityChange($(this));
});
$(document).on('click', '.add_product_cart_search', function () {

  const $btn = $(this);
  const code = $btn.data('code');

  invoice_add_product_card(code);

});
$(document).on('click', '.remove-item', function () {

  let row = $(this).closest('tr');
  removeRow(row);
  $('#search').focus();

});
$(document).on('keydown', '.row-cant', function (e) {

  if (e.key === 'Enter') {

    e.preventDefault(); // Evita comportamiento raro

    $('#search').focus().select(); // focus y selecciona texto

  }

});
$(document).on('change', '#payment', function () {
  recalculatePricesByPaymentType();
});
$(document).on('click', '.product_send', function (e) {
  e.preventDefault();

  // Obtenemos el producto completo que guardamos en el modal
  const productOriginal = $('#product_panel').data('product');
  if (!productOriginal) return;

  // Tomamos los valores editables del modal
  const cant = parseFloat($('#product_cant').val()) || 1;
  const priceOne = parseFloat($('#price_one').val()) || productOriginal.price_one;
  const priceTwo = parseFloat($('#price_two').val()) || productOriginal.price_two;
  const priceCard = parseFloat($('#price_card').val()) || productOriginal.price_card;
  const cantTwo = parseFloat($('#cant_price_two').val()) || productOriginal.cant_two;

  // Creamos un nuevo objeto combinando **datos originales + cambios**
  const productRow = {
    ...productOriginal,   // id, code, description, stock, iva, cost
    price_one: priceOne,
    price_two: priceTwo,
    price_card: priceCard,
    cant_two: cantTwo
  };

  // Llamamos a la función que agrega la fila al carrito
  createRow(productRow, cant);

  // Cerramos el modal
  $('#product_panel').hide();

  // Limpiamos inputs
  $('#product_cant').val(1);
});
//Ocultar panel Products verificado
$("#product_panel").hide();
formatMoneyText();