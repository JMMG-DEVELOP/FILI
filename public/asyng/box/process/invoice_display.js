async function multi_payment_hide() {
  asyng_hide_view({
    id: 'display_multi_payment',
    effect: 'fade',
    clear: true
  });
  await invoice_panel_load();
  $("#search").focus().select();

  return;
}
async function invoice_multi_payment(change, customer) {
  try {

    const response = await asyngAjaxSend(
      'box/process/invoice_multi_payment_load'
    );

    if (response.status) {

      asyng_show_view({
        id: 'display_multi_payment',
        html: response.html,
        effect: 'fade',
        callback: () => {
          asyngMoneyMask();
          $('#cash_digits_mount').val(change);
          $('#cash_credit_customer').val(customer);
          $('#cash_credit_mount').val(change);

        }
      });
    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor cash_credit_confirm', 'danger');

  }

}
async function invoice_cash_verify() {

  let payment = $('#cash_payment').inputmask('unmaskedvalue');

  let change = parseFloat(
    $('#change')
      .text()
      .replace(/\./g, '')
      .replace(',', '.')
  ) || 0;

  const customer = $('#customer_name').val();
  const sales = Number($('#sales').val());

  if (sales !== 1) {
    return;
  }

  if (payment === '' || parseInt(payment) === 0) {
    showAlert('Ingresa el monto', 'danger');
    return;
  }

  if (change >= 0) {

    await sales_cash_payment();

    return;
  }

  // MULTIPAGO
  change = Math.abs(change);

  await invoice_multi_payment(change, customer);
}
async function invoice_cash_panel_load() {
  try {

    const response = await asyngAjaxSend(
      'box/process/invoice_cash_panel'
    );

    if (response.status) {

      asyng_show_view({
        id: 'invoice_panel',
        html: response.html,
        effect: 'fade',
        callback: () => {
          asyngMoneyMask();
          $('#cash_grand_total').text(returnGrandTotal());
          $('#cash_payment').focus();
        }

      });
    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor invoice_panel', 'danger');

  }
}

async function sales_send_display() {

  if ($('#cart_invoice tbody tr').length === 0) {
    showAlert('CARRITO VACIO', 'danger');
    return;
  }

  const paymentType = Number($('#payment').val());
  const sales = Number($('#sales').val());

  // =========================
  // ESPERA
  // =========================
  if (sales === 4) {
    wait_save();
    return;
  }

  // =========================
  // DEVOLUCIÓN
  // =========================
  if (sales === 3) {
    devolution();
    return;
  }

  // =========================
  // CRÉDITO
  // =========================
  if (sales === 2) {
    sales_credit_payment();
    return;
  }

  // =========================
  // VENTA CONTADO
  // =========================
  if (sales === 1) {

    // Efectivo
    if (paymentType === 1) {
      await invoice_cash_panel_load();
      return;
    }

    // QR / Transferencia / Tarjeta
    if ([2, 3, 4].includes(paymentType)) {
      await invoice_digist_panel_load();
      return;
    }
  }
}