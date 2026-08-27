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
});

$(document).on('change', '#payment_device', function () {
  $('#payment_device_operation_number').focus();
});

