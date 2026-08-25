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

