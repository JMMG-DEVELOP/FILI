async function sucursal_panel_load() {
  try {
    const response = await asyngAjaxSend(
      'config/sucursal/panel_load'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo cargar el panel.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_sucursals',
      html: response.html,
      effect: 'fade',
      callback: function () {
        sucursal_table_init();
      }
    });

  } catch (err) {
    showAlert(
      'Error de comunicación con el servidor',
      'danger'
    );
  }
}

function sucursal_table_init() {

  const table = $('#table_sucursal');

  if (!table.length) {

    console.warn(
      'No se encontró dentro'
    );

    return;
  }

  AppTable({
    table: '#table_sucursal',
    pageLength: 10,
    serverSide: false,
    processing: false,
    responsive: true,
    searchDelay: 300,
    exportButtons: false
  });
}

async function sucursal_form_new_open() {
  try {
    const response = await asyngAjaxSend(
      'config/sucursal/form_new_open'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_sucursals',
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

async function sucursal_form_new_save() {
  try {
    if (!validateForm('#form_sucursal')) {
      return;
    }
    const data = {
      values: asyngFormData('#form_sucursal')
    };

    const response = await asyngAjaxSend(
      'config/sucursal/form_new_save',
      data
    );

    if (response.status) {
      showAlert(
        response.msg || ' GUARDADA CORRECTAMENTE',
        'success'
      );

      await sucursal_panel_load();
      return;
    }

    showAlert(
      response.msg || response.error || 'NO SE PUDO GUARDAR.',
      'warning'
    );

  } catch (err) {
    showAlert(
      'Error de comunicación con el servidor',
      'danger'
    );
  }
}

async function sucursal_form_edit_open(id) {
  try {
    const data = {
      id: id
    }
    const response = await asyngAjaxSend(
      'config/sucursal/form_edit_open', data
    );

    if (!response.status) {
      showAlert(
        response.msg || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }
    asyng_show_view({
      id: 'panel_sucursals',
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
async function sucursal_form_edit_save() {
  try {
    if (!validateForm('#form_sucursal')) {
      return;
    }

    const values = asyngFormData('#form_sucursal');
    const data = {
      values: values,
      id: $('#sucursal_id').val()
    };

    const response = await asyngAjaxSend(
      'config/sucursal/form_edit_save',
      data
    );

    if (response.status) {
      await sucursal_panel_load();
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
      'Error de comunicación con el servidor',
      'danger'
    );
  }
}
async function sucursal_delete_open(id, name) {
  try {
    const data = {
      id: id,
      name: name,
      type: 'sucursal'
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
      id: 'panel_sucursals',
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
async function sucursal_delete_save(id) {
  try {
    const data = {
      id: id
    };

    const response = await asyngAjaxSend(
      'config/sucursals/delete_save',
      data
    );

    if (response.status) {
      await sucursal_panel_load();
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