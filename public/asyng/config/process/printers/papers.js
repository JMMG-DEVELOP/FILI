async function papers_panel_load() {
  try {
    const response = await asyngAjaxSend(
      'config/papers/panel_load'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo cargar el panel de impresoras.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_papers',
      html: response.html,
      effect: 'fade',
      callback: function () {
        papers_table_init();
      }
    });

  } catch (err) {
    showAlert(
      'Error de comunicación con el servidor',
      'danger'
    );
  }
}

function papers_table_init() {

  const table = $('#table_paper');

  if (!table.length) {

    console.warn(
      'No se encontró dentro de #panel_printers'
    );

    return;
  }

  AppTable({
    table: '#table_paper',
    pageLength: 10,
    serverSide: false,
    processing: false,
    responsive: true,
    searchDelay: 300,
    exportButtons: false
  });
}

async function papers_form_new_open() {
  try {
    const response = await asyngAjaxSend(
      'config/papers/form_new_open'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_papers',
      html: response.html,
      effect: 'fade'
    });

  } catch (err) {

    showAlert(
      'Error de comunicación con el servidor drivers_new_open',
      'danger'
    );
  }
}

async function papers_form_new_save() {
  try {
    if (!validateForm('#form_paper')) {
      return;
    }
    const data = {
      values: asyngFormData('#form_paper')
    };

    const response = await asyngAjaxSend(
      'config/papers/form_new_save',
      data
    );

    if (response.status) {
      showAlert(
        response.msg || ' GUARDADA CORRECTAMENTE',
        'success'
      );

      await papers_panel_load();
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

async function papers_form_edit_open(id) {
  try {
    const data = {
      id: id
    }
    const response = await asyngAjaxSend(
      'config/papers/form_edit_open', data
    );

    if (!response.status) {
      showAlert(
        response.msg || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }
    asyng_show_view({
      id: 'panel_papers',
      html: response.html,
      effect: 'fade'
    });

  } catch (err) {
    showAlert(
      'Error de comunicación con el servidor edit_open',
      'danger'
    );
  }
}
async function papers_form_edit_save() {
  try {
    if (!validateForm('#form_paper')) {
      return;
    }

    const values = asyngFormData('#form_paper');
    const data = {
      values: values,
      id: $('#paper_id').val()
    };

    const response = await asyngAjaxSend(
      'config/papers/form_edit_save',
      data
    );

    if (response.status) {
      await papers_panel_load();
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
async function papers_delete_open(id, name) {
  try {
    const data = {
      id: id,
      name: name,
      type: 'paper'
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
      id: 'panel_papers',
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
async function papers_delete_save(id) {
  try {
    const data = {
      id: id
    };

    const response = await asyngAjaxSend(
      'config/papers/delete_save',
      data
    );

    if (response.status) {
      await papers_panel_load();
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
      'Error de comunicación con el servidor form_edit_save',
      'danger'
    );
  }
}