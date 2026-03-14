// Remover item del carrito

$(document).on('click', '.remove-item', function () {

  const row = $(this).closest('tr');

  row.remove();

  updateCartCount();
  updateGrandTotal();

  $('#search').val('').focus();
  saveCart();
  SoundManager.warning();

});

$(document).on('keydown', '.row-cant', function (e) {

  if (e.key === 'Enter') {

    e.preventDefault();

    $('#search').focus().select();

  }
});

$(document).on('input', '.row-cant', function () {

  const row = $(this).closest('tr');

  let cant = Number($(this).val()) || 0;

  let price = Number(row.find('.row-price').data('price')) || 0;

  let total = cant * price;

  row.find('.row-total')
    .text(formatMoney(total))
    .data('total', total);

  updateGrandTotal();
  saveCart();
});

$(document).on('click', '.row-cant', function () {
  $(this).select();
});

$(document).on('blur', '.row-cant', function () {

  let input = $(this); // el input que se desenfoca
  let row = input.closest('tr'); // fila correspondiente
  let value = input.val().trim();
  let cant = parseFloat(value);

  cant_row_validate(row, input, value, cant);

});

$(document).on('click', '#remove_cart', function () {

  $('#cart_invoice tbody').empty();
  $('#search').focus();
  updateCartCount();
  updateGrandTotal();
  saveCart();
  SoundManager.warning();

});