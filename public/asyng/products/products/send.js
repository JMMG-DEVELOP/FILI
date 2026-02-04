function product_add_open(code = '') {
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

            if (code) {
              $('#products_form #code').val(code);
              $('#products_form #description').focus();
            }
          });
        });

      }

      csrfHash = response.csrfHash;
    },
    'json'
  );
}
function product_edit_open(code = '') {
  $.post(
    BASE_URL + '/products/products/product_edit_open',
    {
      [csrfName]: csrfHash,
      code: code,
    },
    function (response) {

      if (response.status) {
        open_hide('#products_form', '#products_panel', response.form, function () {

          requestAnimationFrame(() => {
            asyngMoneyMask('#products_form .money');
            asyngPercentMask('#products_form .percent');
            asyngNumericStock('#products_form .stock');

            $("#operation_type").val(response.data.type);

            let product = response.data.product;
            asyng_product_form(product);


          });
        });

      }

      csrfHash = response.csrfHash;
    },
    'json'
  );
}


async function product_add_save(dataArray) {

  try {
    const response = await asyngAjaxSend(
      'products/products/product_save',
      dataArray
    );

    if (!response.status && response.error === 'code_exists') {
      showAlert(response.message, 'danger');
      return;
    }

    if (!response.status) {
      showAlert(
        response.message || 'Error al guardar el producto',
        'danger'
      );
      return;
    }

    if (response.status) {
      cancel_open('#products_form', '#products_panel');

      showAlert(
        response.message, 'success'
      );

      return;
    }


  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor', 'danger');
  } finally {
    $btn.prop('disabled', false);
  }

}
async function product_edit_save(dataArray) {

  try {
    const response = await asyngAjaxSend(
      'products/products/product_edit_save',
      dataArray
    );

    if (!response.status && response.error === 'code_exists') {
      showAlert(response.message, 'danger');
      return;
    }

    if (!response.status) {
      showAlert(
        response.message || 'Error al editar el producto',
        'danger'
      );
      return;
    }

    if (response.status) {
      cancel_open('#products_form', '#products_panel');

      showAlert(
        response.message, 'success'
      );
      $('#productsTable').DataTable().ajax.reload(null, false);

      return;
    }


  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor', 'danger');
  } finally {
    $btn.prop('disabled', false);
  }

}

//Abrir Modal de Registrar Productos
$(document).on('click', '.product_add', function (e) {
  e.preventDefault();
  product_add_open();
});

$(document).on('click', '.product_send', async function (e) {
  e.preventDefault();

  const $btn = $(this);

  if ($btn.prop('disabled')) return;
  $btn.prop('disabled', true);

  if (!validateForm('#product_form')) {
    $btn.prop('disabled', false);
    return;
  }

  const dataArray = asyngFormData('#product_form');

  const formData = Object.fromEntries(
    dataArray.map(item => [item.name, item.value])
  );

  if (formData.operation_type === 'add') {

    product_add_save(dataArray);
  } else if (formData.operation_type === 'edit') {

    product_edit_save(dataArray);
  } else {
    $btn.prop('disabled', false);
  }
});

