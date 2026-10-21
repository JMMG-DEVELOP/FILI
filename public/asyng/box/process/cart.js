// LOCAL STORAGE
function saveCart() {

  let cart = [];

  $('#cart_invoice tbody tr').each(function () {

    let row = $(this);

    cart.push({
      id: row.attr('data-id'),
      code: row.attr('data-code'),
      description: row.find('td:eq(2)').text(),
      cant: row.find('.row-cant').val(),

      price_one: row.attr('data-price-one'),
      price_two: row.attr('data-price-two'),
      price_card: row.attr('data-price-card'),
      price_three: row.attr('data-price-three'),
      cant_two: row.attr('data-cant-two'),
      cant_three: row.attr('data-cant-three'),

      cost: row.attr('data-cost'),
      iva: row.attr('data-iva'),

      price: row.find('.row-price').attr('data-price')
    });

  });

  localStorage.setItem('cart_invoice', JSON.stringify(cart));
}
function loadCart() {

  let cart = JSON.parse(localStorage.getItem('cart_invoice'));

  if (!cart) return;

  cart.forEach(item => {

    let product = {
      id: item.id,
      code: item.code,
      description: item.description,
      stock: 0,

      price_one: Number(item.price_one) || 0,
      price_two: Number(item.price_two) || 0,
      price_card: Number(item.price_card) || 0,
      price_three: Number(item.price_three) || 0,

      cant_two: Number(item.cant_two) || 0,
      cant_three: Number(item.cant_three) || 0,

      cost: parseFloat(item.cost) || 0,
      iva: parseFloat(item.iva) || 0
    };

    let cant = parseFloat(item.cant) || 1;
    let price = parseFloat(item.price) || product.price_one;

    createRow(product, cant, price);

  });

}
function clearCart() {
  localStorage.removeItem('cart_invoice');
  $('#cart_invoice tbody').empty();
}
//Estrucutrar datos a cargar en el carrito
function getCode(code) {

  // validar que tenga 13 dígitos y empiece con 20
  if (/^20\d{11}$/.test(code)) {

    return {
      id: 1,
      code: code.substring(0, 7),   // código producto
      extra: code.substring(7)      // peso o precio
    };

  } else
    if (/^24\d{11}$/.test(code)) {

      return {
        id: 2,
        code: code.substring(0, 7),   // código producto
        extra: code.substring(7)      // peso o precio
      };

    } else {
      return {
        id: 0,
        code: code,      // peso o precio
      };
    }
}
function getProductData(data) {
  const sucursal = $('#select_sucursal').val();

  return {
    id: data.id,
    code: data.code,
    description: data.description,

    cost: Number(data.cost) || 0,

    price_one: Number(data.prices?.price_one) || 0,
    price_two: Number(data.prices?.price_two) || 0,
    price_card: Number(data.prices?.price_card) || 0,
    price_three: Number(data.prices?.price_three) || 0,

    cant_two: Number(data.prices?.cant_two) || 0,
    cant_three: Number(data.prices?.cant_three) || 0,

    stock: Number(data.stock?.[sucursal]) || 0,

    iva: Number(data.iva_id ?? data.iva) || 0
  };
}
// function getProductData(data) {

//   return {
//     id: data.id,
//     code: data.code,
//     description: data.description,
//     cost: data.cost,
//     price_one: parseFloat(data.prices.price_one),
//     price_two: parseFloat(data.prices.price_two),
//     cant_two: parseFloat(data.cant_two),
//     price_card: parseFloat(data.prices?.price_card ?? 0),
//     stock: parseFloat(data.stock[1] ?? 0),
//     iva: parseFloat(data.iva)
//   };
// }

// Suma del total del Carrito en Guaranies
function updateGrandTotal() {
  let total = 0;

  $('#cart_invoice tbody tr').each(function () {
    total += parseFloat($(this).find('.row-total').data('total'));
  });

  $('#grand_total').text(formatMoney(total));
  // format = setChange(total);
  // $('#grand_total').text(format);
}
function returnGrandTotal() {
  let total = 0;

  $('#cart_invoice tbody tr').each(function () {
    total += parseFloat($(this).find('.row-total').data('total'));
  });
  return formatMoney(total);
}
// Contar cantidad de lineas del carrito
function updateCartCount() {
  const count = $('#cart_invoice tbody tr').length;
  $('#cart_item').text(count);
}

//seleccionar porcentaje
function getPercent() {
  const salesType = Number($('#sales').val());
  const paymentType = Number($('#payment').val());

  if (salesType === 2) {
    return Number($('#sales_percent').val()) || 0;
  } else {

    if (paymentType === 1 || paymentType === 3) {
      return Number(0);
    } else {
      return Number($('#payment_percent').val()) || 0;
    }
  }
}

function getCant(value = '') {
  if (value.id === 0) {
    return parseFloat($('#product_cant').val());
  }

  if (value.id === 1) {
    let weight = value.extra.substring(0, 5);

    let grams = Number(weight) / 1000;

    return grams;

  }

  if (value.id === 2) {
    let weight = value.extra.substring(0, 5);
    weight = weight.replace(/^0+/, '');
    weight = weight.slice(0);
    return Number(weight) || 1;
  } else {
    return parseFloat($('#product_cant').val());
  }
}
// Seleccionar Precio
function getPrice(product, cant, percent = 0) {

  const quantity = Number(cant) || 0;

  const priceOne = Number(product.price_one) || 0;
  const priceTwo = Number(product.price_two) || 0;
  const priceThree = Number(product.price_three) || 0;

  const cantTwo = Number(product.cant_two) || 0;
  const cantThree = Number(product.cant_three) || 0;

  // Precio seleccionado manualmente.
  const selectedTier = $('input[name="price_tier"]:checked').val();

  let price;

  if (selectedTier === '1') {

    price = priceOne;

  } else if (selectedTier === '2') {

    price = priceTwo;

  } else if (selectedTier === '3') {

    price = priceThree;

  } else {

    // Selección automática por cantidad.
    // Los umbrales en cero no activan ese precio.

    if (cantThree > 0 && quantity >= cantThree) {

      price = priceThree;

    } else if (cantTwo > 0 && quantity >= cantTwo) {

      price = priceTwo;

    } else {

      price = priceOne;
    }
  }

  // Precio manual personalizado, si se ingresó.
  // La selección de los radios tiene prioridad.
  const customPrice = Number(
    String($('#product_price_input').val() || '')
      .replace(/\./g, '')
      .replace(',', '.')
  );

  if (!selectedTier && customPrice > 1) {
    price = customPrice;
  }

  // Aplicar porcentaje.
  percent = Number(percent) || 0;

  if (percent !== 0) {
    price += price * percent / 100;
  }

  return Math.round(price);
}

// Crear Nueva Fila
function createRow(product, cant, price, save = true) {

  const total = cant * price;

  let row = `
    <tr data-id="${product.id}"
    data-code="${product.code}"
    data-price-one="${product.price_one}"
    data-price-two="${product.price_two ?? 0}"
    data-price-card="${product.price_card ?? 0}"
    data-price-three="${product.price_three ?? 0}"
    data-cant-two="${product.cant_two ?? 0}"
    data-cant-three="${product.cant_three ?? 0}"
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

      <td class="row-price"
    data-base-price="${product.price_one}"
    data-price-two="${product.price_two ?? 0}"
    data-price-three="${product.price_three ?? 0}"
    data-price-card="${product.price_card ?? 0}"
    data-cant-two="${product.cant_two ?? 0}"
    data-cant-three="${product.cant_three ?? 0}"
    data-price="${price}">
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

  $('#cart_invoice tbody').prepend(row);

  if (save) {
    updateCartCount();
    updateGrandTotal();
    saveCart();
  }
}
function updateRow(row, product, cantToAdd, percent) {

  let currentCant = Number(row.find('.row-cant').val()) || 0;
  let cant = Number(cantToAdd) || 0;

  let newCant = currentCant + cant;

  const newPrice = getPrice(product, newCant, percent);
  const newTotal = newCant * newPrice;

  row.find('.row-cant').val(newCant);

  row.find('.row-price')
    .text(formatMoney(newPrice))
    .data('price', newPrice);

  row.find('.row-total')
    .text(formatMoney(newTotal))
    .data('total', newTotal);

  updateCartCount();
  updateGrandTotal();
  saveCart();
}
// Buscar Fila Existente en el Carrito
function findRow(code) {
  return $(`#cart_invoice tbody tr[data-code="${code}"]`);
}

function addOrUpdateRowCart(product, cant, price, percent) {

  let existingRow = findRow(product.code);

  if (existingRow.length > 0) {
    updateRow(existingRow, product, cant, percent);
  } else {
    createRow(product, cant, price);
  }
}

// Actualizar carrito por porcenjaje seleccionado
function updateCartPercent() {
  const percent = getPercent();

  $('#cart_invoice tbody tr').each(function () {
    const row = $(this);

    const cant = Number(row.find('.row-cant').val()) || 0;

    const product = {
      price_one: Number(row.attr('data-price-one')) || 0,
      price_two: Number(row.attr('data-price-two')) || 0,
      price_card: Number(row.attr('data-price-card')) || 0,
      price_three: Number(row.attr('data-price-three')) || 0,

      cant_two: Number(row.attr('data-cant-two')) || 0,
      cant_three: Number(row.attr('data-cant-three')) || 0
    };

    const price = getPrice(product, cant, percent);
    const total = cant * price;

    row.find('.row-price')
      .text(formatMoney(price))
      .attr('data-price', price)
      .data('price', price);

    row.find('.row-total')
      .text(formatMoney(total))
      .attr('data-total', total)
      .data('total', total);
  });

  updateGrandTotal();
  saveCart();
}

// Verificado
async function handleVerifySales(code) {

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
        $('#add_cart_type').prop('checked', false);
      }
    });

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor', 'danger');
  }
}

// vALIDAD CANTIDAD DE CARRITO VACIO
function cant_row_validate(row, input, value, cant) {
  // si está vacío o menor que 1, corregimos
  if (value === '' || cant < 1) {
    cant = 1;
    $(input).val(cant); // <-- aquí sí actualizas el input
  }

  // Obtenemos el producto y precio base de la fila
  let product = {
    price_one: Number(row.attr('data-price-one')) || 0,
    price_two: Number(row.attr('data-price-two')) || 0,
    price_card: Number(row.attr('data-price-card')) || 0,
    price_three: Number(row.attr('data-price-three')) || 0,

    cant_two: Number(row.attr('data-cant-two')) || 0,
    cant_three: Number(row.attr('data-cant-three')) || 0
  };

  // recalculamos precio y total para esta fila
  let percent = getPercent(); // si usas porcentajes dinámicos
  let newPrice = getPrice(product, cant, percent);
  let newTotal = cant * newPrice;

  row.find('.row-price')
    .text(formatMoney(newPrice))
    .data('price', newPrice);

  row.find('.row-total')
    .text(formatMoney(newTotal))
    .data('total', newTotal);

  // actualizamos totales generales y contador
  updateGrandTotal();
  updateCartCount();
  saveCart();
  formatInputs();
}

// Adicion al carrito directamente con coma
function product_add_cart_direct() {
  let code = $('#search').val();
  let name = $('#other_name').val();
  const product = {
    id: code,
    code: 'DR' + code,
    description: name,
    cost: code,
    price_one: parseFloat(code),
    price_two: parseFloat(code),
    cant_two: parseFloat(code),
    price_card: parseFloat(code),
    stock: parseFloat(0),
    iva: parseFloat(10)
  };
  let cant = getCant();
  let price = $('#search').val();
  createRow(product, cant, price);
  formatInputs();
}

// PAGO DE CARRITO
async function update_panel_description(product) {
  try {
    // Código
    $('#panel_product_code').text(product.code);

    // Precio 1
    $('#panel_price_one').text(
      formatMoney(product.price_one)
    );

    // Precio 2
    $('#panel_price_two').text(
      formatMoney(product.price_two)
    );

    // Cantidad precio 2
    $('#panel_cant_two').text(
      product.cant_two
    );
    // Precio 3
    $('#panel_price_three').text(
      formatMoney(product.price_three)
    );

    // Cantidad precio 3
    $('#panel_cant_three').text(
      product.cant_three
    );

    // Stock
    $('#panel_product_stock').text(
      product.stock
    );

    // Código del producto para agregar
    $('#btn_add_orders').attr(
      'data-code',
      product.code
    );

    const data = {
      code: product.code
    };

    // Validar si el cliente ya tiene una espera
    const response = await asyngAjaxSend('box/orders/order_validation', data);
    if ($('#btn_add_orders').is(':hidden')) {
      $('#btn_add_orders').show();
    }
    if (response.status) {
      $('#btn_add_orders_text').text('Ya en Pedido');
      $('#btn_add_orders_icon')
        .removeClass('fas fa-list-ol')
        .addClass('fas fa-check');
      return;
    } else {
      $('#btn_add_orders_text').text('Añadir a Pedido');
      $('#btn_add_orders_icon')
        .removeClass('fas fa-check')
        .addClass('fas fa-list-ol');
    }

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el update_panel_description', 'danger');
  }

}