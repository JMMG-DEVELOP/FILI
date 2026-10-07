async function drivers_panel_load() {
  try {
    const response = await asyngAjaxSend(
      'config/drivers/panel_load'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo cargar el panel de impresoras.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_drivers',
      html: response.html,
      effect: 'fade',
      callback: function () {
        drivers_table_init();
      }
    });

  } catch (err) {
    console.error('drivers_panel_load:', err);

    showAlert(
      'Error de comunicación con el servidor drivers_panel_load',
      'danger'
    );
  }
}

function drivers_table_init() {

  const table = $('#table_driver');

  if (!table.length) {

    console.warn(
      'No se encontró #table_printer dentro de #panel_printers'
    );

    return;
  }

  AppTable({
    table: '#table_driver',
    pageLength: 10,
    serverSide: false,
    processing: false,
    responsive: true,
    searchDelay: 300,
    exportButtons: false
  });
}

async function drivers_form_new_open() {
  try {
    const response = await asyngAjaxSend(
      'config/drivers/form_new_open'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_drivers',
      html: response.html,
      effect: 'fade'
    });

  } catch (err) {
    console.error('form_new_open:', err);

    showAlert(
      'Error de comunicación con el servidor drivers_new_open',
      'danger'
    );
  }
}

async function drivers_form_new_save() {
  try {
    if (!validateForm('#form_driver')) {
      return;
    }
    const data = {
      values: asyngFormData('#form_driver')
    };

    const response = await asyngAjaxSend(
      'config/drivers/form_new_save',
      data
    );

    if (response.status) {
      showAlert(
        response.msg || ' GUARDADA CORRECTAMENTE',
        'success'
      );

      await drivers_panel_load();
      return;
    }

    showAlert(
      response.msg || response.error || 'NO SE PUDO GUARDAR.',
      'warning'
    );

  } catch (err) {
    console.error('form_new_save:', err);

    showAlert(
      'Error de comunicación con el servidor printers_form_new_save',
      'danger'
    );
  }
}

async function drivers_form_edit_open(id) {
  try {
    const data = {
      id: id
    }
    const response = await asyngAjaxSend(
      'config/drivers/form_edit_open', data
    );

    if (!response.status) {
      showAlert(
        response.msg || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }
    asyng_show_view({
      id: 'panel_drivers',
      html: response.html,
      effect: 'fade'
    });

  } catch (err) {
    console.error('form_edit_open:', err);

    showAlert(
      'Error de comunicación con el servidor edit_open',
      'danger'
    );
  }
}
async function drivers_form_edit_save() {
  try {
    if (!validateForm('#form_driver')) {
      return;
    }

    const values = asyngFormData('#form_driver');

    values.auto_cut = $('#auto_cut').is(':checked')
      ? 1
      : 2;

    const data = {
      values: values,
      id: $('#driver_id').val()
    };

    const response = await asyngAjaxSend(
      'config/drivers/form_edit_save',
      data
    );

    if (response.status) {
      await drivers_panel_load();
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
      'Error de comunicación con el servidor printers_form_edit_save',
      'danger'
    );
  }
}
async function drivers_delete_open(id, name) {
  try {
    const data = {
      id: id,
      name: name,
      type: 'driver'
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
      id: 'panel_drivers',
      html: response.html,
      effect: 'fade'
    });

  } catch (err) {
    console.error('form_new_open:', err);

    showAlert(
      'Error de comunicación con el servidor delete_open',
      'danger'
    );
  }
}
async function drivers_delete_save(id) {
  try {
    const data = {
      id: id
    };

    const response = await asyngAjaxSend(
      'config/drivers/delete_save',
      data
    );

    if (response.status) {
      await drivers_panel_load();
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
      'Error de comunicación con el servidor printers_form_edit_save',
      'danger'
    );
  }
}