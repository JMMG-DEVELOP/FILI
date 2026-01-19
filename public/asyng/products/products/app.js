function product_add_open() {
  $.post(
    BASE_URL + '/products/products/product_open',
    {
      [csrfName]: csrfHash
    },
    function (response) {

      if (response.status) {
        open_hide('#products_form', '#products_panel', response.form, function () {

          // ⏳ esperar render real
          requestAnimationFrame(() => {
            asyngMoneyMask('#products_form .money');
            asyngPercentMask('#products_form .percent');
            asyngNumericStock('#products_form .stock');

            $("#operation_type").val(response.data.type);
          });
        });

      }

      csrfHash = response.csrfHash;
    },
    'json'
  );
}


function product_add_save() {

}


$(document).on('click', '.product_cancel', function (e) {
  e.preventDefault();
  cancel_open('#products_form', '#products_panel');
});

//Abrir Modal de Registrar Productos
$(document).on('click', '.product_add', function (e) {
  e.preventDefault();
  product_add_open();
});

$(document).on('click', '.product_send', function (e) {
  e.preventDefault();

  hideGlobalAlert();
  if (!validateForm('#product_form')) {
    return;
  }


  // Si pasa la validación
  alert('send');
});



