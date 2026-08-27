async function post_sales() {
  try {
    await clear();

  } catch (err) {
    showAlert('Error loading panels', err);
  }
}

function change_format() {

  let change = parseFloat(
    $('#change')
      .text()
      .replace(/\./g, '')
      .replace(',', '.')
  ) || 0;
  return change = Math.abs(change);

}
function sales_payment_data() {
  paymentType = Number($('#payment').val());
  const sales = Number($('#sales').val());

}
function sales_send_data() {

  let data = {
    payment: asyngFormData('#form_payment'),
    customer: asyngFormData('#form_customer'),
    point: asyngFormData('#form_expedition_point'),
    receipt: Number($('#receipt_type').val()),
    cart: sales_cart_data(),
    change: change_format(),
    cash: $('#cash_payment').inputmask('unmaskedvalue'),
    cash_credit_payment: asyngFormData('#form_cash_credit_payment'),
    cash_digist_payment: asyngFormData('#form_cash_digist_payment'),
    digist_payment: asyngFormData('#form_digist_payment'),

  }
  return data;
}


function sales_cart_data() {

  let items = [];

  let total_price = 0;
  let total_cost = 0;
  let iva_10 = 0;
  let iva_5 = 0;
  let iva_exenta = 0;

  $('#cart_invoice tbody tr').each(function () {

    let row = $(this);
    let cant = parseFloat(row.find('.row-cant').val()) || 0;
    let price = parseFloat(row.find('.row-price').data('price')) || 0;
    let cost = parseFloat(row.data('cost')) || 0;
    let iva = parseInt(row.data('iva')) || 0;
    let total = parseFloat(row.find('.row-total').data('total')) || (cant * price);
    let totalCost = cant * cost;

    // 🔹 ACUMULADORES
    total_price += total;
    total_cost += totalCost;

    // 🔹 CALCULO IVA (Paraguay)
    if (iva === 10) {
      iva_10 += total / 11; // base imponible IVA 10%
    } else if (iva === 5) {
      iva_5 += total / 21; // base imponible IVA 5%
    } else {
      iva_exenta += total;
    }

    // 🔹 ITEM
    items.push({
      product_id: row.data('id'),
      code: row.data('code'),
      cant: cant,
      unit_price: price,
      unit_cost: cost,
      total_price: total,
      total_cost: totalCost,
      iva: iva,

      description: row.find('td:eq(2)').text().trim(),
      cant_two: parseFloat(row.data('cant_two')) || 0,
      price_two: parseFloat(row.data('price-two')) || 0,

    });

  });

  return {
    items: items,

    totals: {
      total_price: total_price,
      total_cost: total_cost,
      total_margin: total_price - total_cost
    },

    iva: {
      iva_10: iva_10,
      iva_5: iva_5,
      exenta: iva_exenta
    }
  };
}

async function sales_cash_credit_payment() {

  try {
    let data = sales_send_data();

    const response = await asyngAjaxSend('box/sales/sales_cash_credit_payment', data);

    if (response.status) {
      showAlert('VENTA REALIZADA', 'success');
      post_sales();
    }
  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor sales_cash_credit_payment', 'danger');
  }
}



async function procedures_payment_send() {
  try {
    let data = sales_send_data();

    const response = await asyngAjaxSend('box/sales/sales_procedures_other_payment', data);

    console.log(response.data);

    if (response.status) {
      showAlert('VENTA REALIZADA', 'success');
      post_sales();
    }

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor procedures_payment_send', 'danger');
  }

}



async function box_movement_send() {
  try {
    let data =
    {
      type: $('#type_movement').val(),
      payment: 1,
      mount: $('#mount').inputmask('unmaskedvalue'),
      box: $('#box').val(),
    }
    const response = await asyngAjaxSend('box/controller/box_movement_send', data);

    if (!response.status) {
      showAlert(response.message || 'ERROR AL GUARDAR MOVIMIENTO DE CAJA', 'danger');
      return;
    }

    showAlert(response.message || 'MOVIMIENTO REGISTRADO', 'success');
    $('#mount').val('');
    $('#type_movement').val(1);
    asyng_hide_view({
      id: 'box_movement_panel',
      effect: 'fade',
      clear: true
    });
    $("#search").focus().select();

    await history_movements_panel();
    return

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor box_movement_send', 'danger');
  }
}
async function sales_credit_payment() {

  try {
    let data = sales_send_data();
    const response = await asyngAjaxSend('box/sales/sales_credit_payment', data);
    if (response.status) {
      showAlert('ANOTADO CORRECTAMENTE EN CREDITO', 'success');
      await post_sales();
    } else {
      showAlert(response.error, 'warning');
    }

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor sales_credit_payment', 'danger');
  }
}
async function sales_cash_payment() {

  try {
    let data = sales_send_data();
    const response = await asyngAjaxSend('box/sales/sales_cash_payment', data);
    if (response.status) {
      showAlert('VENDIDO PAGO EN EFECTIVO', 'success');
      await post_sales();
    } else {
      showAlert('ERROR - Al Procesar la Venta', 'warning');
    }

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor sales_cash_payment', 'danger');
  }
}

async function devolution() {
  try {
    let data = sales_send_data();

    const response = await asyngAjaxSend('box/sales/sales_devolution', data);

    if (response.status) {
      showAlert('DEVOLUCION EXITOSA', 'success');
      post_sales();
    }

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor devolution', 'danger');
  }

}

