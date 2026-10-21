$(document).on('click', '#invoice_form_create_open', async function (e) {
  e.preventDefault();
  await invoice_form_create_open();
});

async function invoice_form_create_open() {
  try {
    const response = await asyngAjaxSend(
      'invoice/create/form_open'
    );

    if (!response || response.status !== true) {
      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDO CARGAR ',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_home',
      html: response.html,
      effect: 'fade',
      callback: async function () {
        await customer_panel_load();
        await expedition_point_load();
        await sales_panel();
      }
    });

  } catch (error) {
    console.error(error);

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );
  }
}

async function expedition_point_load() {
  try {

    const response = await asyngAjaxSend(
      'box/process/expedition_point_load'
    );

    if (response.status) {

      asyng_show_view({
        id: 'point_panel',
        html: response.html,
        effect: 'fade',
        callback: () => {
          expedition_point_values();
        }
      });

    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor expedition_point_load', 'danger');

  }
}
$(document).on('click', '#toggle_window', function () {

  const windowPanel = $('#window');
  const button = $(this);

  windowPanel.slideToggle(200, function () {

    const visible = windowPanel.is(':visible');

    button.html(
      visible
        ? '<i class="fa fa-eye-slash"></i>'
        : '<i class="fa fa-eye"></i>'
    );

  });

});

async function customer_panel_load(value = '') {
  try {

    const response = await asyngAjaxSend(
      'customer/process/customer_panel_load', { ci: value || '' }
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

}

$(document).on('click', '#invoice_form_create_close', async function (e) {
  e.preventDefault();
  await home_panel_load();
});