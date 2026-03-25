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

function sales_send_verify() {
  let payment = $('#cash_payment').inputmask('unmaskedvalue');
  let change = parseFloat(
    $('#change').text().replace(/\./g, '').replace(',', '.')
  ) || 0;
  const paymentType = Number($('#payment').val());

  let sales = Number($('#sales').val());;

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
          // Venta directa
          alert(change);
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
function prepare_table_sales() {

}
function sales_send_direct() {

}