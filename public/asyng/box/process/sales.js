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

    console.log(response.data);

    // if (response.status) {
    //   alert('GUARDADO, REVISA BD');
    // }

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor sales_cash_payment', 'danger');
  }
}
function sales_send_verify() {
  let payment = $('#cash_payment').inputmask('unmaskedvalue');
  let change = parseFloat(
    $('#change').text().replace(/\./g, '').replace(',', '.')
  ) || 0;
  const paymentType = Number($('#payment').val());

  const receipt = Number($('#receipt_type').val());

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
          alert(change);
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

function sales_send_data() {
  const payment = asyngFormData('#form_payment');
  const customer = asyngFormData('#form_customer');
  const point = asyngFormData('#form_expedition_point');
  const receipt = Number($('#receipt_type').val());
  const cart = sales_cart_data();
  const change = parseFloat(
    $('#change').text().replace(/\./g, '').replace(',', '.')
  ) || 0;
  const cash = $('#cash_payment').inputmask('unmaskedvalue');

  let data = {
    payment: payment,
    customer: customer,
    point: point,
    receipt: receipt,
    cart: cart,
    change: Math.abs(change),
    cash: cash
  }
  return data;
}