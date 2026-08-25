async function post_sales() {
  try {
    await cancelAll();

  } catch (err) {
    showAlert('Error loading panels', err);
  }
}

function change_calculate() {

  const total = parseFloat(
    $('#grand_total')
      .text()
      .replace(/[^\d,.-]/g, '')
      .replace(/\./g, '')
      .replace(',', '.')
  ) || 0;

  const pago = parseFloat(
    $('#cash_payment').inputmask('unmaskedvalue')
  ) || 0;

  return pago - total;
}
function sales_payment_data() {

}
function sales_send_data() {
  const payment = asyngFormData('#form_payment');
  const customer = asyngFormData('#form_customer');
  const point = asyngFormData('#form_expedition_point');
  const procedure_credit_payment = asyngFormData('#procedure_credit_payment');
  const procedure_other_payment = asyngFormData('#procedure_other_payment');
  // const cash_payment = asynFormData('#cash_payment');


  const receipt = Number($('#receipt_type').val());
  const cart = sales_cart_data();
  const cash = $('#cash_payment').inputmask('unmaskedvalue');
  let change = change_calculate();

  let data = {
    payment: payment,
    customer: customer,
    point: point,
    receipt: receipt,
    cart: cart,
    change: change,
    cash: cash,
    procedure_credit: procedure_credit_payment,
    procedure_other: procedure_other_payment,

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
async function sales_cash_payment() {

  try {
    const sales = Number($('#sales').val());;
    let data = sales_send_data();
    if ([3].includes(sales)) {
      const response = await asyngAjaxSend('box/sales/sales_devolution', data);
      if (response.status) {
        showAlert('DEVOLUCIÓN CORRECTA', 'success');
        post_sales();
      }
    } else {
      const response = await asyngAjaxSend('box/sales/sales_cash_payment', data);
      if (response.status) {
        45('VENTA REALIZADA', 'success');
        post_sales();
      }
    }

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor sales_cash_payment', 'danger');
  }
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

