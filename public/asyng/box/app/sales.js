$(document).on('keydown', '#cash_payment', function (e) {

  if (e.key === 'Escape') {
    e.preventDefault();
    let value = $('#cash_payment').inputmask('unmaskedvalue');
    sales_send_verify();
  }

  if (e.key === 'Enter') {

    e.preventDefault();

    $('#search').focus();
    $('#display_escape').hide();
  }

});

$(document).on('keydown', '#receipt_type', function (e) {

  if (e.key === 'Escape') {

    e.preventDefault();

    alert('send');

  }

});