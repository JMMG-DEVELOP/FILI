$(document).ready(function () {

  panels_load();
  loadCart();

});


$(document).on('keydown', '#product_cant', async function (e) {
  if (e.key === 'Enter' && !e.repeat) {
    $('#search').focus();
  }
});


// *******************
// Manejo de Porcentajes y tipo de pago
// 

$(document).on('change', '#sales', function () {
  toggleSales();
  updateCartPercent();
});

$(document).on('change', '#sales_percent', function () {
  updateCartPercent();
});

$(document).on('change', '#payment', function () {

  togglePayment();
  updateCartPercent();

});

$(document).on('change', '#payment_percent', function () {
  updateCartPercent();
});


// Envio de productos a carrito desde formulario
$(document).on('click', '.product_form_send_cart', function (e) {
  e.preventDefault();

  const baseProduct = $('#product_panel').data('product');

  if (!baseProduct) {
    showAlert('Producto no cargado', 'warning');
    return;
  }

  const type = Number($('#add_cart_type').val());

  const productRow = {
    ...baseProduct,
    code: 'VF' + baseProduct.code,
    price_one: parseFloat($('#price_one').val()) || baseProduct.price_one,
    price_two: parseFloat($('#price_two').val()) || baseProduct.price_two,
    price_card: parseFloat($('#price_card').val()) || baseProduct.price_card,
    cant_two: parseFloat($('#cant_price_two').val()) || baseProduct.cant_two,
    description: $('#description').val() || baseProduct.description
  };

  let cant = parseFloat($('#product_cant').val()) || 1;

  let manualPrice = parseFloat($('#product_price_input').val()) || 0;

  let percent = getPercent();

  let price;

  if (manualPrice > 0) {
    price = manualPrice;
  } else {
    price = getPrice(productRow, cant, percent);
  }

  addOrUpdateRowCart(productRow, cant, price, percent);

  $('#product_panel')
    .removeData('product')
    .fadeOut();

  $('#product_cant').val(1);
  $('#product_price_input').val('');

  $('#add_cart_type').val('1').trigger('change');

  $("#search").val('').focus().select();
});

// ENVIO DIRECTO CON COMA Y NOMBRE
$(document).on('keydown', '#other_name', function (e) {

  if (e.key === 'Enter') {

    e.preventDefault();

    const input = $(this);
    const value = input.val().trim();

    if (value === '') {
      input.focus().select();
      return;
    }

    product_add_cart_direct();

    $('#search').val('').focus().select();
    input.hide();

  }

});

// Atajos de TECLADO
$(document).on('keydown', function (e) {

  // CTRL + P → Precio Directo
  if (e.ctrlKey && e.key.toLowerCase() === 'p') {
    e.preventDefault(); // evita imprimir (print)

    $('#product_price_input').slideDown(200).focus();

  }

  // CTRL + B → Tipo de Venta
  if (e.ctrlKey && e.key.toLowerCase() === 'b') {
    e.preventDefault(); // evita pegar (paste)

    $('#sales').focus().select2('open');
  }

  // CTRL + x → Cerrar Busquedas
  if (e.ctrlKey && e.key.toLowerCase() === 'x') {
    e.preventDefault(); // evita pegar (paste)
    searh_panel_close()
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

// Operacones enter focus
$(document).on('keydown', '#product_price_input', function (e) {

  if (e.key === 'Enter') {

    e.preventDefault();

    $('#search').focus().select();

  }

});
// *********
// BOTONES DE CERRAR
$(document).on('click', '.product_form_cancel', function () {

  asyng_hide_view({
    id: 'product_panel',
    effect: 'fade',
    clear: true
  });
  $("#search").focus().select();

  return;
});