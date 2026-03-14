// LOCAL STORAGE
function saveCart() {

  let cart = [];

  $('#cart_invoice tbody tr').each(function () {

    let row = $(this);

    cart.push({
      id: row.data('id'),
      code: row.data('code'),
      description: row.find('td:eq(2)').text(),
      cant: row.find('.row-cant').val(),
      price: row.find('.row-price').data('price')
    });

  });

  localStorage.setItem('cart_invoice', JSON.stringify(cart));
}
function loadCart() {

  let cart = JSON.parse(localStorage.getItem('cart_invoice'));

  if (!cart) return;

  cart.forEach(item => {

    createRow({
      id: item.id,
      code: item.code,
      description: item.description
    }, item.cant, item.price);

  });

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

  return {
    id: data.id,
    code: data.code,
    description: data.description,
    cost: data.cost,
    price_one: parseFloat(data.prices.price_one),
    price_two: parseFloat(data.prices.price_two),
    cant_two: parseFloat(data.cant_two),
    price_card: parseFloat(data.prices?.price_card ?? 0),
    stock: parseFloat(data.stock[1] ?? 0),
    iva: parseFloat(data.iva)
  };
}

// Suma del total del Carrito en Guaranies
function updateGrandTotal() {
  let total = 0;

  $('#cart_invoice tbody tr').each(function () {
    total += parseFloat($(this).find('.row-total').data('total'));
  });

  $('#grand_total').text(formatMoney(total));
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
function getPrice(product, cant, percent) {
  let price;
  let price_input = Number($('#product_price_input').val());

  if (!isNaN(price_input) && price_input > 1) {
    return price_input;
  }

  if (cant < product.cant_two || product.cant_two <= 0) {
    price = product.price_one;
  } else {
    price = product.price_two;
  }
  if (percent === 0) {
    return price;
  } else {
    return price + (price * percent / 100);
  }
}
// Crear Nueva Fila
function createRow(product, cant, price) {

  const total = cant * price;

  let row = `
    <tr data-id="${product.id}"
        data-code="${product.code}"
        data-stock="${product.stock}"
        data-price-one="${price}"
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
          data-base-price="${price}"
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

  // Insertamos arriba
  $('#cart_invoice tbody').prepend(row);

  // Actualizamos contador y totales
  updateCartCount();
  updateGrandTotal();
  saveCart();
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

    let row = $(this);

    let cant = parseFloat(row.find('.row-cant').val()) || 0;

    let basePrice = parseFloat(row.find('.row-price').data('base-price')) || 0;

    let finalPrice = basePrice + (basePrice * percent / 100);

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
    price_one: parseFloat(row.find('.row-price').data('base-price')) || 0,
    cant_two: 0 // si tu lógica de price_two no aplica aquí
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
  createRow(product, cant, price)
}