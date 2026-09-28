async function printers_panel_load() {
  try {
    const response = await asyngAjaxSend(
      'config/printers/panel_load'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo cargar el panel de impresoras.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_printers',
      html: response.html,
      effect: 'fade',
      callback: function () {
        printers_table_init();
      }
    });

  } catch (err) {
    console.error('printers_panel_load:', err);

    showAlert(
      'Error de comunicación con el servidor printers_panel_load',
      'danger'
    );
  }
}

function printers_table_init() {

  const table = $('#table_printer');

  if (!table.length) {

    console.warn(
      'No se encontró #table_printer dentro de #panel_printers'
    );

    return;
  }

  AppTable({
    table: '#table_printer',
    pageLength: 10,
    serverSide: false,
    processing: false,
    responsive: true,
    searchDelay: 300,
    exportButtons: false
  });
}

async function form_new_open() {
  try {
    const response = await asyngAjaxSend(
      'config/printers/form_new_open'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_printers',
      html: response.html,
      effect: 'fade'
    });

  } catch (err) {
    console.error('form_new_open:', err);

    showAlert(
      'Error de comunicación con el servidor printers_new_open',
      'danger'
    );
  }
}

async function form_new_save() {
  try {
    if (!validateForm('#form_printer')) {
      return;
    }

    const printers = asyngFormData('#form_printer');

    printers.auto_cut = $('#auto_cut').is(':checked')
      ? 1
      : 2;

    const data = {
      printers: printers
    };

    const response = await asyngAjaxSend(
      'config/printers/form_new_save',
      data
    );

    if (response.status) {
      showAlert(
        response.msg || 'IMPRESORA GUARDADA CORRECTAMENTE',
        'success'
      );

      await printers_panel_load();
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

async function form_edit_open(id) {
  try {
    const data = {
      id: id
    }
    const response = await asyngAjaxSend(
      'config/printers/form_edit_open', data
    );

    if (!response.status) {
      showAlert(
        response.msg || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }
    asyng_show_view({
      id: 'panel_printers',
      html: response.html,
      effect: 'fade'
    });

  } catch (err) {
    console.error('form_edit_open:', err);

    showAlert(
      'Error de comunicación con el servidor printers_edit_open',
      'danger'
    );
  }
}
async function form_edit_save() {
  try {
    if (!validateForm('#form_printer')) {
      return;
    }

    const values = asyngFormData('#form_printer');

    values.auto_cut = $('#auto_cut').is(':checked')
      ? 1
      : 2;

    const data = {
      values: values,
      id: $('#printer_id').val()
    };

    const response = await asyngAjaxSend(
      'config/printers/form_edit_save',
      data
    );

    if (response.status) {
      await printers_panel_load();
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
    console.error('form_edit_save:', err);

    showAlert(
      'Error de comunicación con el servidor printers_form_edit_save',
      'danger'
    );
  }
}

async function delete_open(id, name) {
  try {
    const data = {
      id: id,
      name: name,
      type: 'printer'
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
      id: 'panel_printers',
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
async function delete_save(id) {
  try {
    const data = {
      id: id
    };

    const response = await asyngAjaxSend(
      'config/printers/delete_save',
      data
    );

    if (response.status) {
      await printers_panel_load();
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