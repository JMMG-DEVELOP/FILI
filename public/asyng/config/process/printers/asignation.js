async function asignation_panel_load() {
  try {
    const response = await asyngAjaxSend(
      'config/asignation/panel_load'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo cargar el panel de impresoras.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_asignation',
      html: response.html,
      effect: 'fade',
      callback: function () {
        asignation_table_init();
      }
    });

  } catch (err) {
    showAlert(
      'Error de comunicación con el servidor ',
      'danger'
    );
  }
}

function asignation_table_init() {

  const table = $('#table_asignation');

  if (!table.length) {

    console.warn(
      'No se encontró table'
    );

    return;
  }

  AppTable({
    table: '#table_asignation',
    pageLength: 10,
    serverSide: false,
    processing: false,
    responsive: true,
    searchDelay: 300,
    exportButtons: false
  });
}

async function asignation_form_new_open() {
  try {
    const response = await asyngAjaxSend(
      'config/asignation/form_new_open'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_asignation',
      html: response.html,
      effect: 'fade'
    });

  } catch (err) {
    console.error('form_new_open:', err);

    showAlert(
      'Error de comunicación con el servidor ',
      'danger'
    );
  }
}

async function asignation_form_new_save() {
  try {
    if (!validateForm('#form_asignation')) {
      return;
    }

    const values = asyngFormData('#form_asignation');


    const data = {
      values: values
    };

    const response = await asyngAjaxSend(
      'config/asignation/form_new_save',
      data
    );

    if (response.status) {
      showAlert(
        response.msg || 'IMPRESORA GUARDADA CORRECTAMENTE',
        'success'
      );

      await asignation_panel_load();
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

async function asignation_form_edit_open(id) {
  try {
    const data = {
      id: id
    }
    const response = await asyngAjaxSend(
      'config/asignation/form_edit_open', data
    );

    if (!response.status) {
      showAlert(
        response.msg || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }
    asyng_show_view({
      id: 'panel_asignation',
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
async function asignation_form_edit_save() {
  try {
    if (!validateForm('#form_asignation')) {
      return;
    }

    const values = asyngFormData('#form_asignation');

    const data = {
      values: values,
      id: $('#asignation_id').val()
    };

    const response = await asyngAjaxSend(
      'config/asignation/form_edit_save',
      data
    );

    if (response.status) {
      await asignation_panel_load();
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

async function asignation_delete_open(id, name) {
  try {
    const data = {
      id: id,
      name: name,
      type: 'asignation'
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
      id: 'panel_asignation',
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
async function asignation_delete_save(id) {
  try {
    const data = {
      id: id
    };

    const response = await asyngAjaxSend(
      'config/asignation/delete_save',
      data
    );

    if (response.status) {
      await asignation_panel_load();
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
      'Error de comunicación con el servidor ',
      'danger'
    );
  }
}