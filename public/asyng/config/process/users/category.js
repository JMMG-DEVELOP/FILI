async function category_panel_load() {
  try {
    const response = await asyngAjaxSend(
      'config/category/panel_load'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo cargar el panel.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_category',
      html: response.html,
      effect: 'fade',
      callback: function () {
        category_table_init();
      }
    });

  } catch (err) {
    showAlert(
      'Error de comunicación con el servidor',
      'danger'
    );
  }
}

function category_table_init() {

  const table = $('#table_category');

  if (!table.length) {

    console.warn(
      'No se encontró tabla'
    );

    return;
  }

  AppTable({
    table: '#table_category',
    pageLength: 10,
    serverSide: false,
    processing: false,
    responsive: true,
    searchDelay: 300,
    exportButtons: false
  });
}

async function category_form_new_open() {
  try {
    const response = await asyngAjaxSend(
      'config/category/form_new_open'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_category',
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

async function category_form_new_save() {
  try {
    if (!validateForm('#form_category')) {
      return;
    }
    const data = {
      values: asyngFormData('#form_category')
    };

    const response = await asyngAjaxSend(
      'config/category/form_new_save',
      data
    );

    if (response.status) {
      showAlert(
        response.msg || ' GUARDADA CORRECTAMENTE',
        'success'
      );

      await category_panel_load();
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

async function category_form_edit_open(id) {
  try {
    const data = {
      id: id
    }
    const response = await asyngAjaxSend(
      'config/category/form_edit_open', data
    );

    if (!response.status) {
      showAlert(
        response.msg || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }
    asyng_show_view({
      id: 'panel_category',
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
async function category_form_edit_save() {
  try {
    if (!validateForm('#form_category')) {
      return;
    }

    const values = asyngFormData('#form_category');

    const data = {
      values: values,
      id: $('#category_id').val()
    };

    const response = await asyngAjaxSend(
      'config/category/form_edit_save',
      data
    );

    if (response.status) {
      await category_panel_load();
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
async function category_delete_open(id, name) {
  try {
    const data = {
      id: id,
      name: name,
      type: 'category'
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
      id: 'panel_category',
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
async function category_delete_save(id) {
  try {
    const data = {
      id: id
    };

    const response = await asyngAjaxSend(
      'config/category/delete_save',
      data
    );

    if (response.status) {
      await category_panel_load();
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