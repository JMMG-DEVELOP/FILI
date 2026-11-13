async function provider_panel_load() {
  try {
    const response = await asyngAjaxSend(
      'products/provider/panel_load'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo cargar el panel.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'provider_panel',
      html: response.html,
      effect: 'fade',
      callback: function () {
        provider_table_init();
      }
    });

  } catch (err) {
    console.error('provider_panel_load:', err);

    showAlert(
      'Error de comunicación con el servidor',
      'danger'
    );
  }
}

function provider_table_init() {

  const table = $('#table_provider');

  if (!table.length) {

    console.warn(
      'No se encontró '
    );

    return;
  }

  AppTable({
    table: '#table_provider',
    pageLength: 10,
    serverSide: false,
    processing: false,
    responsive: true,
    searchDelay: 300,
    exportButtons: false
  });
}

async function provider_form_new_open() {
  try {
    const response = await asyngAjaxSend(
      'products/provider/form_new_open'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'provider_panel',
      html: response.html,
      effect: 'fade'
    });

  } catch (err) {

    showAlert(
      'Error de comunicación con el servidor ',
      'danger'
    );
  }
}

async function provider_form_new_save() {
  try {
    if (!validateForm('#form_provider')) {
      return;
    }
    const data = {
      values: asyngFormData('#form_provider')
    };

    const response = await asyngAjaxSend(
      'products/provider/form_new_save',
      data
    );

    if (response.status) {
      showAlert(
        response.msg || ' GUARDADA CORRECTAMENTE',
        'success'
      );

      await provider_panel_load();
      return;
    }

    showAlert(
      response.msg || response.error || 'NO SE PUDO GUARDAR.',
      'warning'
    );

  } catch (err) {

    showAlert(
      'Error de comunicación con el servidor ',
      'danger'
    );
  }
}

async function provider_form_edit_open(id) {
  try {
    const data = {
      id: id
    }
    const response = await asyngAjaxSend(
      'products/provider/form_edit_open', data
    );

    if (!response.status) {
      showAlert(
        response.msg || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }
    asyng_show_view({
      id: 'provider_panel',
      html: response.html,
      effect: 'fade'
    });

  } catch (err) {

    showAlert(
      'Error de comunicación con el servidor ',
      'danger'
    );
  }
}
async function provider_form_edit_save() {
  try {
    if (!validateForm('#form_provider')) {
      return;
    }

    const values = asyngFormData('#form_provider');

    values.auto_cut = $('#auto_cut').is(':checked')
      ? 1
      : 2;

    const data = {
      values: values,
      id: $('#provider_id').val()
    };

    const response = await asyngAjaxSend(
      'products/provider/form_edit_save',
      data
    );

    if (response.status) {
      await provider_panel_load();
      showAlert(
        response.msg || 'EDITADO CORRECTAMENTE',
        'success'
      );
      return;
    }

    showAlert(
      response.msg || response.error || 'NO SE PUDO MODIFICAR',
      'warning'
    );

  } catch (err) {

    showAlert(
      'Error de comunicación con el servidor ',
      'danger'
    );
  }
}
async function provider_delete_open(id, name) {
  try {
    const data = {
      id: id,
      name: name,
      type: 'provider'
    };
    const response = await asyngAjaxSend(
      'config/delete_open', data
    );

    if (!response.status) {
      showAlert(
        response.error || 'NO SE PUDO ABRIR EL FORMULARIO',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'provider_panel',
      html: response.html,
      effect: 'fade'
    });

  } catch (err) {

    showAlert(
      'Error de comunicación con el servidor ',
      'danger'
    );
  }
}
async function provider_delete_save(id) {
  try {
    const data = {
      id: id
    };

    const response = await asyngAjaxSend(
      'products/provider/delete_save',
      data
    );

    if (response.status) {
      await provider_panel_load();
      showAlert(
        response.msg,
        'success'
      );
      return;
    }

    showAlert(
      response.msg || response.error || 'NO SE PUDO ELIMINAR',
      'warning'
    );

  } catch (err) {

    showAlert(
      'Error de comunicación con el servidor',
      'danger'
    );
  }
}