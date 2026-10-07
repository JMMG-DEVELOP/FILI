async function devices_panel_load() {
  try {
    const response = await asyngAjaxSend(
      'config/devices/panel_load'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo cargar el panel .',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_devices',
      html: response.html,
      effect: 'fade',
      callback: function () {
        devices_table_init();
      }
    });

  } catch (err) {
    showAlert(
      'Error de comunicación con el servidor',
      'danger'
    );
  }
}

function devices_table_init() {

  const table = $('#table_devices');

  if (!table.length) {

    console.warn(
      'No se encontró dentro de #PANEL'
    );

    return;
  }

  AppTable({
    table: '#table_devices',
    pageLength: 10,
    serverSide: false,
    processing: false,
    responsive: true,
    searchDelay: 300,
    exportButtons: false
  });
}

async function devices_form_edit_open(id) {
  try {
    const data = {
      id: id
    }
    const response = await asyngAjaxSend(
      'config/devices/form_edit_open', data
    );

    if (!response.status) {
      showAlert(
        response.msg || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }
    asyng_show_view({
      id: 'panel_devices',
      html: response.html,
      effect: 'fade'
    });

  } catch (err) {
    showAlert(
      'Error de comunicación con el servidor',
      'danger'
    );
  }
}
async function devices_form_edit_save() {
  try {
    if (!validateForm('#form_devices')) {
      return;
    }

    const values = asyngFormData('#form_devices');
    const data = {
      values: values,
      id: $('#devices_id').val()
    };

    const response = await asyngAjaxSend(
      'config/devices/form_edit_save',
      data
    );

    if (response.status) {
      await devices_panel_load();
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
      'Error de comunicación con el servidor form_edit_save',
      'danger'
    );
  }
}
async function devices_delete_open(id, name) {
  try {
    const data = {
      id: id,
      name: name,
      type: 'device'
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
      id: 'panel_devices',
      html: response.html,
      effect: 'fade'
    });

  } catch (err) {
    showAlert(
      'Error de comunicación con el servidor delete_open',
      'danger'
    );
  }
}
async function devices_delete_save(id) {
  try {
    const data = {
      id: id
    };

    const response = await asyngAjaxSend(
      'config/devices/delete_save',
      data
    );

    if (response.status) {
      await devices_panel_load();
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