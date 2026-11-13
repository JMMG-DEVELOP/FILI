$(document).on('click', '.product_print', function (e) {
  e.preventDefault();
  const code = $(this).data('code');
  product_print_open(code);
});

async function product_print_open(code) {
  alert(code + 'imprimir')
}