$(document).ready(function () {

  panels_load();
  loadCart();
  asyngMoneyMask();
  $('#display_escape').hide();
  $('#display_other_pay').hide();


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
  $('#search').focus();
});

$(document).on('change', '#sales_percent', function () {
  updateCartPercent();
  $('#search').focus();
});

$(document).on('change', '#payment', function () {

  togglePayment();
  updateCartPercent();
  $('#search').focus();
});

$(document).on('change', '#payment_percent', function () {
  updateCartPercent();
  $('#search').focus();
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
    e.preventDefault();
    $('#product_price_input').slideDown(200).focus();

  }

  // CTRL + B → Tipo de Venta
  if (e.altKey && e.key.toLowerCase() === 'b') {
    e.preventDefault(); // evita pegar (paste)

    $('#sales').focus().select2('open');
  }

  // ALT + T → Focus en Cantidad
  if (e.altKey && e.key.toLowerCase() === 't') {
    e.preventDefault();

    $('#product_cant').focus();
    $('#product_cant').select();
  }

  // ALT + F → fACTURACION EN CLIENTE
  if (e.altKey && e.key.toLowerCase() === 'f') {
    e.preventDefault();

    $('#customer_name').focus().select();
  }

  // Detectar ALT + C cambiar a credito
  if (e.altKey && e.key.toLowerCase() === 'c') {
    e.preventDefault();
    $('#sales').val('2').trigger('change');
    $('#ruc_ci').select().focus();
  }
  // Detectar ALT + C cambiar a CONTADO
  if (e.altKey && e.key.toLowerCase() === 'z') {
    e.preventDefault();
    $('#sales').val('1').trigger('change');
    $('#search').select().focus();
  }
  if (e.altKey && e.key.toLowerCase() === 'd') {
    e.preventDefault();
    $('#sales').val('3').trigger('change');
    $('#search').select().focus();
  }
  // CAMBIAR TIPO DE PAGO
  if (e.altKey && e.key.toLowerCase() === '1') {
    e.preventDefault();
    $('#payment').val('1').trigger('change');
    $('#search').select().focus();
  }
  if (e.altKey && e.key.toLowerCase() === '2') {
    e.preventDefault();
    $('#payment').val('2').trigger('change');
    $('#search').select().focus();
  }
  if (e.altKey && e.key.toLowerCase() === '3') {
    e.preventDefault();
    $('#payment').val('3').trigger('change');
    $('#search').select().focus();
  }
  if (e.altKey && e.key.toLowerCase() === '4') {
    e.preventDefault();
    $('#payment').val('4').trigger('change');
    $('#search').select().focus();
  }

  if (e.altKey && e.key.toLowerCase() === 'l') {
    e.preventDefault();
    cancelAll();
  }
  // Presionar Escape y Cerrar Venta
  if (e.key === 'Escape') {
    e.preventDefault();
    sales_send_display();
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