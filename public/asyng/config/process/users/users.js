async function users_panel_load() {
  try {
    const response = await asyngAjaxSend(
      'config/users/panel_load'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo cargar el panel.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_users',
      html: response.html,
      effect: 'fade',
      callback: function () {
        users_table_init();
      }
    });

  } catch (err) {
    showAlert(
      'Error de comunicación con el servidor',
      'danger'
    );
  }
}

function users_table_init() {

  const table = $('#table_users');

  if (!table.length) {

    console.warn(
      'No se encontró tabla'
    );

    return;
  }

  AppTable({
    table: '#table_users',
    pageLength: 10,
    serverSide: false,
    processing: false,
    responsive: true,
    searchDelay: 300,
    exportButtons: false
  });
}

async function users_form_new_open() {
  try {
    const response = await asyngAjaxSend(
      'config/users/form_new_open'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_users',
      html: response.html,
      effect: 'fade',

    });

  } catch (err) {

    showAlert(
      'Error de comunicación con el servidor drivers_new_open',
      'danger'
    );
  }
}

async function users_form_new_save() {
  try {
    if (!validateForm('#form_users')) {
      return;
    }
    const data = {
      values: asyngFormData('#form_users')
    };

    const response = await asyngAjaxSend(
      'config/users/form_new_save',
      data
    );

    if (response.status) {
      showAlert(
        response.msg || ' GUARDADA CORRECTAMENTE',
        'success'
      );

      await users_panel_load();
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

async function users_form_edit_open(id) {
  try {
    const data = {
      id: id
    }
    const response = await asyngAjaxSend(
      'config/users/form_edit_open', data
    );

    if (!response.status) {
      showAlert(
        response.msg || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }
    asyng_show_view({
      id: 'panel_users',
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
async function users_form_edit_save() {
  try {
    if (!validateForm('#form_users')) {
      return;
    }

    const values = asyngFormData('#form_users');

    const data = {
      values: values,
      id: $('#users_id').val()
    };

    const response = await asyngAjaxSend(
      'config/users/form_edit_save',
      data
    );

    if (response.status) {
      await users_panel_load();
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

async function users_form_edit_password_save() {

  try {

    if (!validateForm('#form_password')) {
      return;
    }

    const data = {
      id: $('#form_password input[name="id"]').val(),
      values: asyngFormData('#form_password')
    };

    const response = await asyngAjaxSend(
      'config/users/form_edit_password_save',
      data
    );

    if (response.status) {
      await users_panel_load();
      showAlert(
        response.msg || 'CONTRASEÑA ACTUALIZADA CORRECTAMENTE',
        'success'
      );

      $('#form_password')[0].reset();

      return;
    }

    showAlert(
      response.msg || response.error || 'NO SE PUDO ACTUALIZAR LA CONTRASEÑA.',
      'warning'
    );

  } catch (err) {

    console.error(err);

    showAlert(
      'Error de comunicación con el servidor',
      'danger'
    );
  }
}
async function users_delete_open(id, name) {
  try {
    const data = {
      id: id,
      name: name,
      type: 'user'
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
      id: 'panel_users',
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
async function users_delete_save(id) {
  try {
    const data = {
      id: id
    };

    const response = await asyngAjaxSend(
      'config/users/delete_save',
      data
    );

    if (response.status) {
      await users_panel_load();
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