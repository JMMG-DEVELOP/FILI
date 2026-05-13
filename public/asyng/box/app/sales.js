$(document).on('keydown', '#cash_payment', function (e) {

  if (e.key === 'Escape') {
    e.preventDefault();
    let value = $('#cash_payment').inputmask('unmaskedvalue');
    sales_send_verify();
  }

  if (e.key === 'Enter') {

    e.preventDefault();

    $('#search').focus();
    $('#cash_payment').val('');
    $('#display_escape').hide();
    procedures_hide();
  }

});

$(document).on('change', '#receipt_type', function (e) {
  $('#cash_payment').focus();

});

$(document).on('click', '.procedures_send', function () {
  sales_cash_credit_payment();
});

$(document).on('click', '.procedures_credit_send', function () {
  sales_cash_credit_payment();
});

$(document).on('click', '.procedures_payment_send', function () {
  let paymentType = $('#value_payment_type').val();

  if (!paymentType) {

    showAlert('SELECIONAR TIPO DE PAGO COMPLEMENTARIO', 'warning');

    return;
  }
  procedures_payment_send()

});

$(document).on('click', '.procedures_hide', function () {
  procedures_hide();
});