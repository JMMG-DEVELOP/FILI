// Eventos 
$(document).on('click', '.product_form_add_open', function (e) {
  e.preventDefault();
  product_form_open('Nuevo Producto', 'new', 'home_panel')
});
$(document).on('click', '.product_edit', function (e) {
  e.preventDefault();
  const code = $(this).data('code');
  product_form_open('Edición de Producto', 'edit', 'home_panel', code)
});

$(document).on('click', '.product_form_close', function (e) {
  e.preventDefault();
  home_panel_load()
});


async function product_form_open(title, type, div, code = '', page = 'product') {
  await window_view(
    'products/products/form/product/open',
    div,
    {
      title: title,
      type: type,
      code: code,
      page: page
    },
    function (response) {
      formatGuaraniesInputs('#' + div);
      if (type === 'new') {
        $('#code').focus();
      } else {
        $('#description').focus();
        if (typeof window.initializeProductMargins === 'function') {
          window.initializeProductMargins();
        }
      }
    }
  );
}


async function code_validation(code) {

  const response = await asyngAjaxSend(
    'products/products/form/product/code_validation',
    {
      code: code
    },
  );

  if (response.status) {
    showAlert(
      'CODIGO DISPONIBLE',
      'warning'
    );
    product_form_open('Nuevo Producto', 'new', 'home_panel', code)
  } else {
    showAlert(
      'INGRESANDO A EDITAR PRODUCTO',
      'warning'
    );
    product_form_open('Edición de Producto', 'edit', 'home_panel', code)
  }


}

