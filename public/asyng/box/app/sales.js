$(document).on('keydown', '#cash_payment', async function (e) {

  if (e.key === 'Escape') {

    e.preventDefault();
    e.stopPropagation();

    await invoice_cash_verify();

    return;
  }

  if (e.key === 'Enter') {

    e.preventDefault();
    e.stopPropagation();

    await invoice_panel_load();

    $('#search').focus();

    $('#cash_payment').val('');

    $('#display_escape').hide();

    multi_payment_hide();

    return;
  }

});

$(document).on('click', '.multi_payment_hide', function () {
  multi_payment_hide();
});

$(document).on('change', '.cash_digits_payment', function () {
  $('#cash_digits_payment').focus();
});

$(document).on('keydown', '#payment_device_operation_number', async function (e) {
  if (e.key === 'Enter') {

    e.preventDefault();
    e.stopPropagation();

    await invoice_panel_load();

    $('#search').focus();
    $('#cash_payment').val('');
    $('#display_escape').hide();
    return;
  }

  if (e.key === 'Escape') {
    e.preventDefault();
    e.stopPropagation();

    const operationNumber = $(this).val().trim();

    if (!operationNumber) {

      showAlert(
        'INGRESA EL NÚMERO DE OPERACIÓN',
        'warning'
      );

      $(this).focus();

      return;
    } else {
      await sales_digist_payment();
    }
  }
});

$(document).on('change', '#payment_device', function () {
  $('#payment_device_operation_number').focus();
});

$(document).on('click', '.multi_payment_cash_credit', function () {
  sales_cash_credit_payment();
});
$(document).on('click', '.multi_payment_cash_digits', function () {
  sales_cash_digist_payment();

});
$(document).on('click', '.box_movement_send', function () {
  box_movement_send();

});

$(document).on('click', '.history_sales_detail', function () {
  const id = $(this).data('id');

  history_sales_detail_panel(id);

});

$(document).on('click', '.history_sales_detail_close', function () {
  history_sales_panel();

});

$(document).on('change', '#receipt_type', function () {

  const receiptType = $(this).val();

  if (receiptType === '2') {
    // Fiscal → FACTURA
    $('#print_type').val('2').trigger('change');
    $('#customer_name').focus().select();

  } else if (receiptType === '1') {
    // Ticket → TICKET
    $('#print_type').val('1').trigger('change');
    $('#search').focus();
  }
  borderFlash('#print_type', '#receipt_type', '#customer_name')

});