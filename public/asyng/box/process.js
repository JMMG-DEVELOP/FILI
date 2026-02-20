
async function product_search(values) {

  try {
    const response = await asyngAjaxSend(
      'box/controller/product_search',
      values
    );

    if (response.status) {
      asyng_show_view({
        id: 'search_panel',
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
  let percent = Number($('#card_percent').val());

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
function getPriceByQuantity(product, quantity) {
  const paymentType = Number($('#payment').val());
  if (paymentType == 4) {
    let percent = Number($('#card_percent').val());
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

  // if (newCant > product.stock) {
  //   alert('Supera el stock disponible');
  //   return;
  // }

  const newPrice = getPriceByQuantity(product, newCant);
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
// function handleQuantityChange(input) {

//   let row = input.closest('tr');

//   let stock = parseFloat(row.data('stock'));
//   let priceOne = parseFloat(row.data('price-one'));
//   let priceTwo = parseFloat(row.data('price-two'));
//   let cantTwo = parseFloat(row.data('cant-two'));

//   let cant = parseFloat(input.val());

//   if (!cant || cant <= 0) cant = 1;

//   let price;

//   if (cantTwo == 0 || cant < cantTwo) {
//     price = priceOne;
//   } else {
//     price = priceTwo;
//   }

//   let total = cant * price;

//   row.find('.row-price')
//     .text(formatMoney(price))
//     .data('price', price);

//   row.find('.row-total')
//     .text(formatMoney(total))
//     .data('total', total);

//   updateGrandTotal();
// }
function handleQuantityChange(input) {

  let row = input.closest('tr');

  let cant = parseFloat(input.val());
  let stock = parseFloat(row.data('stock'));
  let priceOne = parseFloat(row.data('price-one'));
  let priceTwo = parseFloat(row.data('price-two'));
  let cantTwo = parseFloat(row.data('cant-two'));
  let payment = Number($('#payment').val());
  let percent = Number($('#card_percent').val());
  if (!cant || cant <= 0) cant = 1;

  if (cant > stock) {
    input.val(stock);
    cant = stock;
  }

  let finalPrice;

  // 🔴 TARJETA ID 4
  if (payment === 4) {

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
function handleVerifySales(response) {
  if (!response.status) return;
  const product = getProductData(response.data);

  verifyAddFormProduct(product);

  // Guardamos **todo el producto original** en el modal
  $('#product_panel').data('product', product);

  // Abrir modal
  $("#product_panel").show();
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
          handleVerifySales(response);
          resetInputs()
          break;
      }
    }



  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor', 'danger');
  } finally {
    // $btn.prop('disabled', false);
  }
}
