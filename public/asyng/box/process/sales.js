async function post_sales() {
  try {
    await cancelAll();
    await payment_panel_load();
    await customer_panel_load();
    await expedition_point_load();

  } catch (err) {
    showAlert('Error loading panels', err);
  }
}

function change_calculate() {

  let total = parseFloat(
    $('#grand_total')
      .text()
      .replace(/[^\d,.-]/g, '')
      .replace(/\./g, '')
      .replace(',', '.')
  ) || 0;

  let pago = parseFloat(
    $('#cash_payment')
      .val()
      .replace(/[^\d,.-]/g, '')
      .replace(/\./g, '')
      .replace(',', '.')
  ) || 0;

  let change = pago - total;

  return change;
}
function sales_send_data() {
  const payment = asyngFormData('#form_payment');
  const customer = asyngFormData('#form_customer');
  const point = asyngFormData('#form_expedition_point');
  const procedure_credit_payment = asyngFormData('#procedure_credit_payment');
  const procedure_other_payment = asyngFormData('#procedure_other_payment');


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

function sales_send_display() {
  if ($('#cart_invoice tbody tr').length > 0) {
    const paymentType = Number($('#payment').val());
    const sales = Number($('#sales').val());;

    if ([2, 3, 4].includes(paymentType) || [2, 3, 4].includes(sales)) {

      SoundManager.payment();
      $('#display_other_pay').slideDown(200);
      $('#display_escape').hide(200);

    } else {

      SoundManager.payment();
      $('#display_escape').slideDown(200);
      $('#cash_payment').focus();
      $('#display_other_pay').hide();

    }

  }


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
      iva: iva
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
    let data = sales_send_data();

    const response = await asyngAjaxSend('box/sales/sales_cash_payment', data);
    if (response.status) {
      showAlert('VENTA REALIZADA', 'success');
      post_sales();
    }
  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor sales_cash_payment', 'danger');
  }
}
async function sales_cash_credit_confirm(change, customer) {
  try {

    const response = await asyngAjaxSend(
      'box/process/sales_cash_credit_confirm'
    );

    if (response.status) {

      asyng_show_view({
        id: 'display_procedures',
        html: response.html,
        effect: 'fade',
        callback: () => {
          $('#value_credit_mount').val(change);
          $('#value_credit_customer').val(customer);
          $('#value_payment_mount').val(change);

          asyngMoneyMask();
        }
      });
    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor cash_credit_confirm', 'danger');

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

function sales_send_verify() {
  let payment = $('#cash_payment').inputmask('unmaskedvalue');
  let change = parseFloat(
    $('#change').text().replace(/\./g, '').replace(',', '.')
  ) || 0;
  const paymentType = Number($('#payment').val());

  const receipt = Number($('#receipt_type').val());

  const customer = $('#customer_name').val();

  let sales = Number($('#sales').val());

  if ([1].includes(sales)) {
    if ([2, 3, 4].includes(paymentType)) {
      // PAGO NO EN EFECTIVO

    } else {
      // PAGO EN EFECTIVO
      if (payment === '' || parseInt(payment) === 0) {
        showAlert('Ingresa el monto', 'danger');
        return;
      } else {
        if (change >= 0) {
          sales_cash_payment();

        } else if (change < 0) {
          // Saldoo en credito
          change = Math.abs(change);
          sales_cash_credit_confirm(change, customer);
        }

      }

    }
  } else if ([2].includes(sales)) {
    // CREDITO
    alert('credito');
  } else if ([3].includes(sales)) {
    // DEVOLUCION
    alert('devolucion');
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

function procedures_hide() {
  asyng_hide_view({
    id: 'display_procedures',
    effect: 'fade',
    clear: true
  });
  $("#search").focus().select();

  return;
}