
//Cerrar todos los deslizamientos o tablas 

function searh_panel_close(callback) {
  asyng_hide_view({
    id: 'product_search_panel',
    effect: 'fade',
    clear: true,
  });
  asyng_hide_view({
    id: 'customer_search_panel',
    effect: 'fade',
    clear: true
  });
  asyng_hide_view({
    id: 'product_panel',
    effect: 'fade',
    clear: true
  });
  asyng_hide_view({
    id: 'customer_panel',
    effect: 'fade',
    clear: true,
    callback: callback
  });

}
async function product_search(values) {

  try {
    const response = await asyngAjaxSend(
      'box/controller/product_search',
      values
    );

    if (response.status) {
      asyng_show_view({
        id: 'product_search_panel',
        html: response.html,
        effect: 'fade'
      });
    }


  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor', 'danger');
  } finally {
    $btn.prop('disabled', false);
  }

}
// ********* INVOICE
// Recalcular Precios al cambiar a Card tipo de pago
function recalculatePricesByPaymentType() {

  const paymentType = Number($('#payment').val());
  const isCard = paymentType === 4;
  const isQr = paymentType === 3;
  let percent = Number($('#payment_percent').val());

  $('#cart_invoice tbody tr').each(function () {

    let row = $(this);

    let cant = parseFloat(row.find('.row-cant').val());
    let priceOne = parseFloat(row.data('price-one'));
    let priceTwo = parseFloat(row.data('price-two'));
    let cantTwo = parseFloat(row.data('cant-two'));

    let finalPrice;

    // 🔴 TARJETA (ID 4)
    if (isCard) {

      finalPrice = priceOne + (priceOne * percent / 100);

    }
    // 🟢 NORMAL
    else {

      if (cantTwo == 0 || cantTwo < cant) {
        finalPrice = priceOne;
      } else {
        finalPrice = priceTwo;
      }

    }

    let total = cant * finalPrice;

    row.find('.row-price')
      .text(formatMoney(finalPrice))
      .data('price', finalPrice);

    row.find('.row-total')
      .text(formatMoney(total))
      .data('total', total);

  });

  updateGrandTotal();
}
// Contar cantidad de codigos
function updateCartCount() {
  const count = $('#cart_invoice tbody tr').length;
  $('#cart_item').text(count);
}
// Remover Item
function removeRow(row) {

  row.fadeOut(150, function () {
    $(this).remove();

    updateGrandTotal();
    updateCartCount();
  });

}
// Obtener Datos del Producto
function getProductData(data) {

  return {
    id: data.id,
    code: data.code,
    description: data.description,
    price_one: parseFloat(data.prices.price_one),
    price_two: parseFloat(data.prices.price_two),
    cant_two: parseFloat(data.cant_two),
    price_card: parseFloat(data.prices?.price_card ?? 0),
    stock: parseFloat(data.stock[1] ?? 0),
    iva: parseFloat(data.iva)
  };
}
// Validar Cantidades
function validateQuantity(cant, stock) {

  if (!cant || cant <= 0) {
    showAlert('Cantidad Invalida', 'warning');
    SoundManager.error();
    return false;
  }

  return true;
}
// Eleccion de Precios
function getPriceByQuantity(product, quantity, percent) {
  const paymentType = Number($('#payment').val());
  if (paymentType == 4) {
    return product.price_one + (product.price_one * percent / 100);;
  }
  if (paymentType == 2) {
    return product.price_one + (product.price_one * percent / 100);;
  }
  // Si no tiene precio por cantidad
  if (product.cant_two == 0) {
    return product.price_one;
  }

  // Si cant_two es menor que la cantidad ingresada
  if (quantity < product.cant_two) {
    return product.price_one;
  }
  // Caso contrario
  return product.price_two;
}
// Buscar Fila Existente en Invoice
function findRow(code) {
  return $('#cart_invoice tbody').find(`tr[data-code="${code}"]`);
}
// Crear Nueva Fila
function createRow(product, cant) {

  const price = getPriceByQuantity(product, cant); // calcula price_one/price_two/card
  const total = cant * price;

  let row = `
    <tr data-id="${product.id}"
        data-code="${product.code}"
        data-stock="${product.stock}"
        data-price-one="${product.price_one}"
        data-price-two="${product.price_two}"
        data-price-card="${product.price_card}"
        data-cant-two="${product.cant_two}"
        data-cost="${product.cost ?? 0}"
        data-iva="${product.iva ?? 0}">
      <td>${product.code}</td>
      <td>
        <input type="number"
               class="form-control form-control-sm row-cant"
               value="${cant}"
               min="1"
               style="width:80px">
      </td>
      <td>${product.description}</td>
      <td class="row-price" data-price="${price}">
        ${formatMoney(price)}
      </td>
      <td class="row-total" data-total="${total}">
        ${formatMoney(total)}
      </td>
      <td class="text-center">
        <button class="btn btn-sm btn-danger remove-item">
          <i class="fas fa-trash"></i>
        </button>
      </td>
    </tr>
  `;

  // Insertamos arriba
  $('#cart_invoice tbody').prepend(row);

  // Actualizamos contador y totales
  updateCartCount();
  updateGrandTotal();
}

// Actualizar Fila
function updateRow(row, product, cantToAdd) {

  let currentCant = parseFloat(row.find('.row-cant').val());
  let newCant = currentCant + cantToAdd;
  let percent = Number($('#payment_percent').val());

  const newPrice = getPriceByQuantity(product, newCant, percent);
  const newTotal = newCant * newPrice;

  row.find('.row-cant').val(newCant);

  row.find('.row-price')
    .text(formatMoney(newPrice))
    .data('price', newPrice);

  row.find('.row-total')
    .text(formatMoney(newTotal))
    .data('total', newTotal);
}

// Agregar o Actualizar Fila de Invoice
function addOrUpdateRow(product, cant) {

  let existingRow = findRow(product.code);

  if (existingRow.length > 0) {
    updateRow(existingRow, product, cant);
  } else {
    createRow(product, cant);
  }
}
// Actualizar Total General
function updateGrandTotal() {
  let total = 0;

  $('#cart_invoice tbody tr').each(function () {
    total += parseFloat($(this).find('.row-total').data('total'));
  });

  $('#grand_total').text(formatMoney(total));
}
// Resetear Input
function resetInputs() {
  $('#product_cant').val(1);
  $('#search').val('').focus();
}
// Detectar Cambios en Boleta

function handleQuantityChange(input) {

  let row = input.closest('tr');

  let cant = parseFloat(input.val());
  let stock = parseFloat(row.data('stock'));
  let priceOne = parseFloat(row.data('price-one'));
  let priceTwo = parseFloat(row.data('price-two'));
  let cantTwo = parseFloat(row.data('cant-two'));
  let payment = Number($('#payment').val());
  let percent = Number($('#payment_percent').val());
  if (!cant || cant <= 0) cant = 1;

  let finalPrice;

  // 🔴 TARJETA ID 4
  if (payment == 4) {
    finalPrice = product.price_one + (product.price_one * percent / 100);;
  }
  if (payment == 2) {
    finalPrice = product.price_one + (product.price_one * percent / 100);;
  }
  // 🟢 NORMAL
  else {

    if (cantTwo == 0 || cantTwo < cant) {
      finalPrice = priceOne;
    } else {
      finalPrice = priceTwo;
    }

  }

  let total = cant * finalPrice;

  row.find('.row-price')
    .text(formatMoney(finalPrice))
    .data('price', finalPrice);

  row.find('.row-total')
    .text(formatMoney(total))
    .data('total', total);

  updateGrandTotal();
}

// Agregar de manera Directa
function handleDirectSale(response) {

  if (!response.status) return;

  const product = getProductData(response.data);

  const cant = parseFloat($('#product_cant').val());

  if (!validateQuantity(cant, product.stock)) return;

  addOrUpdateRow(product, cant);

  updateGrandTotal();

  resetInputs();
}

// Agregar de manera Verificada

function verifyAddFormProduct(product) {

  if (!product) return;

  $("#description").val(product.description ?? '');
  $("#price_one").val(product.price_one ?? 0);
  $("#price_two").val(product.price_two ?? 0);
  $("#cant_price_two").val(product.cant_two ?? 0);
  $("#price_card").val(product.price_card ?? 0);


}
async function handleVerifySales(code, r) {

  try {
    const response = await asyngAjaxSend(
      'box/controller/product_form',
      { value: code }
    );

    if (!response.status) {
      showAlert(response.message, 'warning');
      return;
    }
    asyng_show_view({
      id: 'product_panel',
      html: response.html,
      effect: 'fade',
      callback: function () {
        $('#product_panel').data('product', response.product);
      }
    });

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor', 'danger');
  }
}
async function invoice_add_product_card(code) {

  try {
    const response = await asyngAjaxSend(
      'box/invoice/product_add',
      { code: code }
    );

    if (!response.status) {
      showAlert(response.message, 'danger');
      $('#product_cant').val(1);
      $('#search').val('').focus();
      SoundManager.error();
      return;
    } else {
      const type = Number($('#add_card_type').val());

      switch (type) {
        case 1:
          handleDirectSale(response);
          break;

        case 2:
          handleVerifySales(code);
          resetInputs()
          break;
      }
    }

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor', 'danger');
  }
}


// ****************
// CUSTOMER

async function customer_load(value = '') {
  try {
    const response = await asyngAjaxSend(
      'box/controller/customer', { ci: value || '' }
    );

    if (!response.status) {
      showAlert('ERROR', 'warning')
    } else {
      asyng_show_view({
        id: 'customer_panel',
        html: response.html,
        effect: 'fade'
      });
    }

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor', 'danger');
  }
}
async function customer_search(value) {
  try {
    const response = await asyngAjaxSend(
      'box/controller/customer_search',
      { value: value }
    );

    if (!response.status) {
      showAlert('error', 'warning')
    } else {
      asyng_show_view({
        id: 'customer_search_panel',
        html: response.html,
        effect: 'fade'
      });
    }

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor', 'danger');
  }
}


// ****************
// MAYMENT

// Carga vista de tipo de pago

async function payment_load() {
  try {
    const response = await asyngAjaxSend(
      'payment/controller/customer', { ci: value || '' }
    );

    if (!response.status) {
      showAlert('ERROR', 'warning')
    } else {
      asyng_show_view({
        id: 'payment_panel',
        html: response.html,
        effect: 'fade'
      });
    }

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor', 'danger');
  }
}

/* ============================================================
   POS / INVOICE MODULE
   Arquitectura limpia y centralizada
   ============================================================ */

const Cart = {
  items: [],
  paymentType: 1,
  salesType: 1,
  paymentPercent: 0,
  salesPercent: 0
};

/* ============================================================
   UTILIDADES
   ============================================================ */

function formatMoney(value) {
  return new Intl.NumberFormat('es-PY').format(Math.round(value));
}

function updateCartCount() {
  $('#cart_item').text(Cart.items.length);
}

/* ============================================================
   CALCULO CENTRAL DE PRECIO
   ============================================================ */

function calculatePrice(product, quantity) {

  let basePrice;

  if (product.cant_two == 0 || quantity < product.cant_two) {
    basePrice = product.price_one;
  } else {
    basePrice = product.price_two;
  }

  // CARD o QR
  if (Cart.paymentType == 4 || Cart.paymentType == 2) {
    basePrice += basePrice * Cart.paymentPercent / 100;
  }

  // CREDITO
  if (Cart.salesType == 2) {
    basePrice += basePrice * Cart.salesPercent / 100;
  }

  return basePrice;
}

/* ============================================================
   TOTAL GENERAL
   ============================================================ */

function updateGrandTotal() {

  let total = 0;

  Cart.items.forEach(product => {

    const price = calculatePrice(product, product.quantity);

    total += price * product.quantity;

  });

  $('#grand_total').text(formatMoney(total));
}

/* ============================================================
   RENDERIZAR CARRITO
   ============================================================ */

function renderCart() {

  const tbody = $('#cart_invoice tbody');

  tbody.html('');

  Cart.items.forEach(product => {

    const price = calculatePrice(product, product.quantity);
    const total = price * product.quantity;

    tbody.append(`
<tr data-code="${product.code}">

<td>${product.code}</td>

<td>
<input type="number"
class="form-control form-control-sm row-cant"
value="${product.quantity}"
min="1"
style="width:80px">
</td>

<td>${product.description}</td>

<td class="row-price">${formatMoney(price)}</td>

<td class="row-total">${formatMoney(total)}</td>

<td class="text-center">
<button class="btn btn-danger btn-sm remove-item">
<i class="fas fa-trash"></i>
</button>
</td>

</tr>
`);

  });

  updateGrandTotal();
  updateCartCount();
}

/* ============================================================
   OPERACIONES DEL CARRITO
   ============================================================ */

function addProduct(product, quantity) {

  let existing = Cart.items.find(p => p.code === product.code);

  if (existing) {
    existing.quantity += quantity;
  } else {

    Cart.items.push({
      ...product,
      quantity: quantity
    });

  }

  renderCart();
}

function removeProduct(code) {

  Cart.items = Cart.items.filter(p => p.code !== code);

  renderCart();
}

function updateQuantity(code, quantity) {

  let item = Cart.items.find(p => p.code === code);

  if (!item) return;

  item.quantity = quantity;

  renderCart();
}

/* ============================================================
   OBTENER DATOS DE PRODUCTO
   ============================================================ */

function getProductData(data) {

  return {
    id: data.id,
    code: data.code,
    description: data.description,
    price_one: parseFloat(data.prices.price_one),
    price_two: parseFloat(data.prices.price_two),
    cant_two: parseFloat(data.cant_two),
    stock: parseFloat(data.stock[1] ?? 0),
    iva: parseFloat(data.iva)
  };

}

/* ============================================================
   VALIDACIONES
   ============================================================ */

function validateQuantity(cant, stock) {

  if (!cant || cant <= 0) {
    showAlert('Cantidad Invalida', 'warning');
    SoundManager.error();
    return false;
  }

  return true;
}

/* ============================================================
   EVENTOS DEL CARRITO
   ============================================================ */

$(document).on('input', '.row-cant', function () {

  const code = $(this).closest('tr').data('code');
  const quantity = parseFloat($(this).val());

  updateQuantity(code, quantity);

});

$(document).on('click', '.remove-item', function () {

  const code = $(this).closest('tr').data('code');

  removeProduct(code);

});

/* ============================================================
   CAMBIOS DE FORMULARIO
   ============================================================ */

$('#payment').on('change', function () {

  Cart.paymentType = Number($(this).val());

  renderCart();

});

$('#sales').on('change', function () {

  Cart.salesType = Number($(this).val());

  renderCart();

});

$('#payment_percent').on('change', function () {

  Cart.paymentPercent = Number($(this).val());

  renderCart();

});

$('#sales_percent').on('change', function () {

  Cart.salesPercent = Number($(this).val());

  renderCart();

});

/* ============================================================
   RESET INPUTS
   ============================================================ */

function resetInputs() {

  $('#product_cant').val(1);
  $('#search').val('').focus();

}

/* ============================================================
   AGREGAR PRODUCTO DIRECTO
   ============================================================ */

function handleDirectSale(response) {

  if (!response.status) return;

  const product = getProductData(response.data);

  const cant = parseFloat($('#product_cant').val());

  if (!validateQuantity(cant, product.stock)) return;

  addProduct(product, cant);

  resetInputs();
}

/* ============================================================
   AGREGAR PRODUCTO DESDE API
   ============================================================ */

async function invoice_add_product_card(code) {

  try {

    const response = await asyngAjaxSend(
      'box/invoice/product_add',
      { code: code }
    );

    if (!response.status) {

      showAlert(response.message, 'danger');
      SoundManager.error();
      resetInputs();
      return;

    }

    const type = Number($('#add_card_type').val());

    switch (type) {

      case 1:
        handleDirectSale(response);
        break;

      case 2:
        handleVerifySales(code);
        resetInputs();
        break;

    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor', 'danger');

  }

}

/* ============================================================
   BUSQUEDA PRODUCTOS
   ============================================================ */

async function product_search(values) {

  try {

    const response = await asyngAjaxSend(
      'box/controller/product_search',
      values
    );

    if (response.status) {

      asyng_show_view({
        id: 'product_search_panel',
        html: response.html,
        effect: 'fade'
      });

    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor', 'danger');

  }

}

/* ============================================================
   CERRAR PANELES
   ============================================================ */

function searh_panel_close(callback) {

  asyng_hide_view({
    id: 'product_search_panel',
    effect: 'fade',
    clear: true,
  });

  asyng_hide_view({
    id: 'customer_search_panel',
    effect: 'fade',
    clear: true
  });

  asyng_hide_view({
    id: 'product_panel',
    effect: 'fade',
    clear: true
  });

  asyng_hide_view({
    id: 'customer_panel',
    effect: 'fade',
    clear: true,
    callback: callback
  });

}