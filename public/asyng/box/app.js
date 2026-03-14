// // Bootones de Cancelar y cerrar busquedas y formulario
// $(document).on('click', '.search_cancel', function () {

//   searh_panel_close(customer_load());
//   $("#search").focus();

//   return;
// });
// $(document).on('click', '.product_search_cancel', function () {

//   asyng_hide_view({
//     id: 'product_search_panel',
//     effect: 'fade',
//     clear: true
//   });
//   $("#search").focus();

//   return;
// });
// $(document).on('click', '.product_form_cancel', function () {

//   asyng_hide_view({
//     id: 'product_panel',
//     effect: 'fade',
//     clear: true
//   });
//   $("#search").focus();

//   return;
// });
// $(document).on('click', '.customer_search_cancel', function () {

//   asyng_hide_view({
//     id: 'customer_search_panel',
//     effect: 'fade',
//     clear: true
//   });
//   $("#search").focus();

//   return;
// });

// // Atajos de Teclado
// $(document).on('keydown', '#product_cant', async function (e) {
//   if (e.key === 'Enter' && !e.repeat) {
//     $('#search').focus();
//   }
// });
// $(document).on('keydown', '#add_card', async function (e) {
//   if (e.key === 'Enter' && !e.repeat) {
//     $('#search').focus();
//   }
// });
// $(document).on('keydown', function (e) {

//   // CTRL + P → Tipo de Pago
//   if (e.ctrlKey && e.key.toLowerCase() === 'p') {
//     e.preventDefault(); // evita imprimir (print)

//     $('#payment').focus().select2('open');

//   }

//   // CTRL + B → Tipo de Venta
//   if (e.ctrlKey && e.key.toLowerCase() === 'b') {
//     e.preventDefault(); // evita pegar (paste)

//     $('#sales').focus().select2('open');
//   }

//   // CTRL + x → Cerrar Busquedas
//   if (e.ctrlKey && e.key.toLowerCase() === 'x') {
//     e.preventDefault(); // evita pegar (paste)
//     searh_panel_close()
//     $("#search").focus();

//     return;
//   }

//   // CTRL + T → Focus en Cantidad
//   if (e.ctrlKey && e.key.toLowerCase() === 'y') {
//     e.preventDefault();

//     $('#product_cant').focus();
//     $('#product_cant').select();
//   }

//   // CTRL + A → fOCUS EN TIPO DE ADICION A CARRITO
//   if (e.ctrlKey && e.key.toLowerCase() === 'a') {
//     e.preventDefault();

//     $('#add_card').focus();
//   }

//   // Presionar Escape y Cerrar Venta
//   if (e.key === 'Escape') {
//     e.preventDefault();

//     SoundManager.payment();
//     // alert('Cerrar Venta');
//   }

// });

// $(document).on('keydown', '#search', async function (e) {

//   let value = $(this).val().trim();

//   let data = {
//     value: value
//   };

//   /**
//    * SHIFT → abrir buscador modal
//    */
//   if (e.key === 'Shift' && !e.repeat) {

//     e.preventDefault();
//     if (!value) {

//       asyng_hide_view({
//         id: 'search_panel',
//         effect: 'fade',
//         clear: true
//       });

//       return;
//     }

//     product_search(data);
//     return;
//   }

//   /**
//    * ENTER → buscar / validar
//    */
//   if (e.key === 'Enter' && !e.repeat) {

//     e.preventDefault();

//     if (!value) {

//       showAlert('Campo Vacío', 'warning');

//       asyng_hide_view({
//         id: 'search_panel',
//         effect: 'fade',
//         clear: true
//       });

//       SoundManager.error();
//       return;
//     }

//     const formatted = value.length <= 7
//       ? value.padStart(7, '0')
//       : value;
//     $(this).val(formatted);

//     invoice_add_product_card(formatted)

//   }

// });

// $(document).on('input', '.row-cant', function () {
// handleQuantityChange($(this));
// });
// $(document).on('click', '.add_product_cart_search', function () {

//   const $btn = $(this);
//   const code = $btn.data('code');

//   invoice_add_product_card(code);

// });
// $(document).on('click', '.remove-item', function () {

//   let row = $(this).closest('tr');
//   removeRow(row);
//   $('#search').focus();

// });
// $(document).on('keydown', '.row-cant', function (e) {

//   if (e.key === 'Enter') {

//     e.preventDefault(); // Evita comportamiento raro

//     $('#search').focus().select(); // focus y selecciona texto

//   }

// });
// $(document).on('change', '#payment', function () {
//   recalculatePricesByPaymentType();
// });
// $(document).on('click', '.product_send_cart', function (e) {
//   e.preventDefault();

//   const baseProduct = $('#product_panel').data('product');

//   if (!baseProduct) {
//     showAlert('Producto no cargado', 'warning');
//     return;
//   }

//   const productRow = {
//     ...baseProduct,
//     price_one: parseFloat($('#price_one').val()) || 0,
//     price_two: parseFloat($('#price_two').val()) || 0,
//     price_card: parseFloat($('#price_card').val()) || 0,
//     cant_two: parseFloat($('#cant_price_two').val()) || 0,
//     description: $('#description').val() || baseProduct.description
//   };

//   createRow(productRow, 1);

//   $('#product_panel')
//     .removeData('product')
//     .fadeOut();
//   $("#search").focus();
// });
// $(document).on('blur', '.row-cant', function () {

//   let value = $(this).val().trim();

//   if (value === '' || parseFloat(value) < 1) {
//     $(this).val(1);
//   }

// });

// // ******************* 
// // CUSTOMER

// $(document).on('keydown', '#ruc_ci', function (e) {

//   // Detectar SHIFT
//   if (e.key === 'Shift' && !e.repeat) {
//     e.preventDefault();
//     let value = $(this).val().trim();
//     customer_search(value);
//   }

// });

// $(document).on('keydown', '#customer_name', function (e) {

//   // Detectar SHIFT
//   if (e.key === 'Shift' && !e.repeat) {
//     e.preventDefault();
//     let value = $(this).val().trim();
//     customer_search(value);
//   }

// });

// $(document).on('click', '.add_customer_form', function () {

//   let ci = $(this).data('ci');
//   let name = $(this).data('name');

//   $('#ruc_ci').val(ci);
//   $('#customer_name').val(name);
//   $('#search').focus();
//   asyng_hide_view({
//     id: 'customer_search_panel',
//     effect: 'fade',
//     clear: true
//   });
// });

// $(document).on('click', '.add_new_customer', async function () {

//   try {
//     const response = await asyngAjaxSend(
//       'box/controller/customer_form'
//     );

//     if (response.status) {
//       asyng_show_view({
//         id: 'customer_panel',
//         html: response.html,
//         effect: 'fade'
//       });
//     }
//   } catch (err) {
//     console.error(err);
//     showAlert('Error de comunicación con el servidor', 'danger');
//   }

// });

// $(document).on('click', '.customer_add_cancel', function () {
//   customer_load();
//   $('#search').focus();
// });
// $(document).on('click', '.customer_send', async function (e) {

//   e.preventDefault();

//   const $btn = $(this);

//   if ($btn.prop('disabled')) return;
//   $btn.prop('disabled', true);

//   if (!validateForm('#customer_form')) {
//     $btn.prop('disabled', false);
//     return;
//   }

//   const dataArray = asyngFormData('#customer_form');
//   const data = Object.fromEntries(
//     dataArray.map(item => [item.name, item.value])
//   );

//   customer_add(data);

//   $btn.prop('disabled', false);
//   $('#search').focus();
// });
// $('#payment_percent').hide();
// $('#sales_percent').hide();


// //*************************** */
// // PAYMENT


// //******************* */
// customer_load();
// asyngMoneyMask();
// formatMoneyText();

/* =========================================================
   EVENTOS GENERALES
========================================================= */

/* Cancelar paneles */

$(document).on('click', '.search_cancel', function () {

  searh_panel_close(() => customer_load())
  $("#search").focus()

})

$(document).on('click', '.product_search_cancel', function () {

  asyng_hide_view({
    id: 'product_search_panel',
    effect: 'fade',
    clear: true
  })

  $("#search").focus()

})

$(document).on('click', '.product_form_cancel', function () {

  asyng_hide_view({
    id: 'product_panel',
    effect: 'fade',
    clear: true
  })

  $("#search").focus()

})

$(document).on('click', '.customer_search_cancel', function () {

  asyng_hide_view({
    id: 'customer_search_panel',
    effect: 'fade',
    clear: true
  })

  $("#search").focus()

})

/* =========================================================
   ATAJOS DE TECLADO POS
========================================================= */

$(document).on('keydown', function (e) {

  /* CTRL + P → Tipo pago */

  if (e.ctrlKey && e.key.toLowerCase() === 'p') {

    e.preventDefault()
    $('#payment').focus().select2('open')

  }

  /* CTRL + B → Tipo venta */

  if (e.ctrlKey && e.key.toLowerCase() === 'b') {

    e.preventDefault()
    $('#sales').focus().select2('open')

  }

  /* CTRL + X → cerrar paneles */

  if (e.ctrlKey && e.key.toLowerCase() === 'x') {

    e.preventDefault()
    searh_panel_close()
    $("#search").focus()

  }

  /* CTRL + Y → cantidad */

  if (e.ctrlKey && e.key.toLowerCase() === 'y') {

    e.preventDefault()
    $('#product_cant').focus().select()

  }

  /* CTRL + A → tipo adicion */

  if (e.ctrlKey && e.key.toLowerCase() === 'a') {

    e.preventDefault()
    $('#add_card').focus()

  }

  /* ESC */

  if (e.key === 'Escape') {

    SoundManager.payment()

  }

})

/* =========================================================
   BUSCADOR PRODUCTO
========================================================= */

$(document).on('keydown', '#search', async function (e) {

  let value = $(this).val().trim()

  let data = { value: value }

  /* SHIFT → buscador */

  if (e.key === 'Shift' && !e.repeat) {

    e.preventDefault()

    if (!value) {

      asyng_hide_view({
        id: 'search_panel',
        effect: 'fade',
        clear: true
      })

      return
    }

    product_search(data)

    return

  }

  /* ENTER */

  if (e.key === 'Enter' && !e.repeat) {

    e.preventDefault()

    if (!value) {

      showAlert('Campo Vacío', 'warning')
      SoundManager.error()
      return

    }

    const formatted = value.length <= 7
      ? value.padStart(7, '0')
      : value

    $(this).val(formatted)

    invoice_add_product_card(formatted)

  }

})

/* =========================================================
   EVENTOS CARRITO
========================================================= */

/* cambiar cantidad */

$(document).on('input', '.row-cant', function () {

  const code = $(this).closest('tr').data('code')
  const qty = parseFloat($(this).val())

  updateQuantity(code, qty)

})

/* eliminar */

$(document).on('click', '.remove-item', function () {

  const code = $(this).closest('tr').data('code')

  removeProduct(code)

  $("#search").focus()

});

/* enter cantidad */

$(document).on('keydown', '.row-cant', function (e) {

  if (e.key === 'Enter') {

    e.preventDefault()
    $("#search").focus().select()

  }

})

/* =========================================================
   AGREGAR PRODUCTO DESDE BUSQUEDA
========================================================= */

$(document).on('click', '.add_product_cart_search', function () {

  const code = $(this).data('code')

  invoice_add_product_card(code)

})

/* =========================================================
   PRODUCTO FORM
========================================================= */

$(document).on('click', '.product_send_cart', function (e) {

  e.preventDefault()

  const baseProduct = $('#product_panel').data('product')

  if (!baseProduct) {

    showAlert('Producto no cargado', 'warning')
    return

  }

  const productRow = {

    ...baseProduct,

    price_one: parseFloat($('#price_one').val()) || 0,
    price_two: parseFloat($('#price_two').val()) || 0,
    price_card: parseFloat($('#price_card').val()) || 0,
    cant_two: parseFloat($('#cant_price_two').val()) || 0,
    description: $('#description').val() || baseProduct.description

  }

  addProduct(productRow, 1)

  $('#product_panel').removeData('product').fadeOut()

  $("#search").focus()

})

/* =========================================================
   CAMBIO DE TIPO DE PAGO
========================================================= */

$('#payment').on('change', function () {

  const payment = Number($(this).val())

  Cart.paymentType = payment

  /* mostrar porcentaje */

  if (payment == 4 || payment == 2) {

    $('#payment_percent').show()

  } else {

    $('#payment_percent').hide()

  }

  renderCart()

})

/* cambiar porcentaje pago */

$('#payment_percent').on('change', function () {

  Cart.paymentPercent = Number($(this).val())

  renderCart()

})

/* =========================================================
   CAMBIO TIPO VENTA
========================================================= */

$('#sales').on('change', function () {

  const sales = Number($(this).val())

  Cart.salesType = sales

  /* credito */

  if (sales == 2) {

    $('#sales_percent').show()

  } else {

    $('#sales_percent').hide()

  }

  renderCart()

})

/* porcentaje credito */

$('#sales_percent').on('change', function () {

  Cart.salesPercent = Number($(this).val())

  renderCart()

})

/* =========================================================
   CUSTOMER
========================================================= */

$(document).on('keydown', '#ruc_ci', function (e) {

  if (e.key === 'Shift' && !e.repeat) {

    e.preventDefault()

    let value = $(this).val().trim()

    customer_search(value)

  }

})

$(document).on('keydown', '#customer_name', function (e) {

  if (e.key === 'Shift' && !e.repeat) {

    e.preventDefault()

    let value = $(this).val().trim()

    customer_search(value)

  }

})

$(document).on('click', '.add_customer_form', function () {

  let ci = $(this).data('ci')
  let name = $(this).data('name')

  $('#ruc_ci').val(ci)
  $('#customer_name').val(name)

  $('#search').focus()

  asyng_hide_view({
    id: 'customer_search_panel',
    effect: 'fade',
    clear: true
  })

})

/* =========================================================
   INIT POS
========================================================= */

$(document).ready(function () {

  customer_load()

  $('#payment_percent').hide()
  $('#sales_percent').hide()

  asyngMoneyMask()
  formatMoneyText()

})