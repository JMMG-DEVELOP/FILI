$(document).on('keydown', '#ruc_ci', function (e) {

  // Detectar SHIFT
  if (e.key === 'Shift' && !e.repeat) {
    e.preventDefault();
    let value = $(this).val().trim();
    customer_search(value);
  }
  if (e.key === 'Enter') {
    e.preventDefault();
    $('#search').focus();
  }

});

$(document).on('keydown', '#customer_name', function (e) {

  // Detectar SHIFT
  if (e.key === 'Shift' && !e.repeat) {
    e.preventDefault();
    let value = $(this).val().trim();
    customer_search(value);
  }
  if (e.key === 'Enter') {
    e.preventDefault();
    $('#search').focus();
  }

});

$(document).on('click', '.add_customer_form', function (e) {
  e.preventDefault();
  let ci = $(this).data('ci');
  let name = $(this).data('name');

  $('#ruc_ci').val(ci);
  $('#customer_name').val(name);
  $('#search').focus();
  asyng_hide_view({
    id: 'customer_search_panel',
    effect: 'fade',
    clear: true
  });
});

$(document).on('click', '.add_new_customer', async function () {

  try {
    const response = await asyngAjaxSend(
      'box/controller/customer_form'
    );

    if (response.status) {
      asyng_show_view({
        id: 'customer_panel',
        html: response.html,
        effect: 'fade'
      });
    }
  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor', 'danger');
  }

});

$(document).on('click', '.customer_send', async function (e) {
  e.preventDefault();
  const $btn = $(this);

  if ($btn.prop('disabled')) return;
  $btn.prop('disabled', true);

  // Validación básica del formulario
  if (!validateForm('#customer_form')) {
    $btn.prop('disabled', false);
    return;
  }

  // Serializamos el formulario en objeto
  const dataArray = asyngFormData('#customer_form');
  const data = Object.fromEntries(dataArray.map(item => [item.name, item.value]));

  // Llamada AJAX
  await customer_add(data);

  $btn.prop('disabled', false);
  $('#search').focus().select();
});

$(document).on('click', '.customer_add_cancel', function () {
  customer_panel_load();
  $('#search').focus();
});

$(document).on('click', '.customer_search_cancel', function () {

  asyng_hide_view({
    id: 'customer_search_panel',
    effect: 'fade',
    clear: true
  });
  $("#search").focus();

  return;
});