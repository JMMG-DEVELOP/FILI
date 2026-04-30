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

$(document).on('keydown', '#receipt_type', function (e) {

  if (e.key === 'Escape') {

    e.preventDefault();

    alert('send');

  }

});

$(document).on('click', '.procedures_send', function () {
  sales_cash_credit_payment();
});
$(document).on('click', '.procedures_credit_send', function () {
  sales_cash_credit_payment();
});
$(document).on('click', '.procedures_hide', function () {
  procedures_hide();
});