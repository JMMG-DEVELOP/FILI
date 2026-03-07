
function getProductData(data) {

  return {
    id: data.id,
    code: data.code,
    description: data.description,
    // cost:data.cost,
    price_one: parseFloat(data.prices.price_one),
    price_two: parseFloat(data.prices.price_two),
    cant_two: parseFloat(data.cant_two),
    price_card: parseFloat(data.prices?.price_card ?? 0),
    stock: parseFloat(data.stock[1] ?? 0),
    iva: parseFloat(data.iva)
  };
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

// Buscar Fila Existente en el Carrito
function findRow(code) {
  return $('#cart_invoice tbody').find(`tr[data-code="${code}"]`);
}
function addOrUpdateRowCart(product, cant) {

  let existingRow = findRow(product.code);

  if (existingRow.length > 0) {
    updateRow(existingRow, product, cant);
  } else {
    createRow(product, cant);
  }
}

function handleDirectSale(response) {

  if (!response.status) return;

  const product = getProductData(response.data);

  const cant = parseFloat($('#product_cant').val());

  addOrUpdateRowCart(product, cant);

  updateGrandTotal();

  resetInputs();
}