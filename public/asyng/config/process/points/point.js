async function point_panel_load() {
  try {
    const response = await asyngAjaxSend(
      'config/point/panel_load'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo cargar el panel',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_expedition_point',
      html: response.html,
      effect: 'fade',

    });

  } catch (err) {

    showAlert(
      'Error de comunicación con el servidor ',
      'danger'
    );
  }
}


async function point_form_new_open() {
  try {
    const response = await asyngAjaxSend(
      'config/point/form_new_open'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_expedition_point',
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

async function point_form_new_save() {
  try {
    if (!validateForm('#form_point')) {
      return;
    }
    const data = {
      values: asyngFormData('#form_point')
    };

    const response = await asyngAjaxSend(
      'config/point/form_new_save',
      data
    );

    if (response.status) {
      showAlert(
        response.msg || ' GUARDADA CORRECTAMENTE',
        'success'
      );

      await point_panel_load()
      await sequence_panel_load()
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

async function point_form_edit_open(id) {
  try {
    const data = {
      id: id
    }
    const response = await asyngAjaxSend(
      'config/point/form_edit_open', data
    );

    if (!response.status) {
      showAlert(
        response.msg || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }
    asyng_show_view({
      id: 'panel_expedition_point',
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
async function point_form_edit_save() {

  try {

    if (!validateForm('#form_point')) {
      return;
    }

    const data = {
      values: asyngFormData('#form_point'),
      id: $('#point_id').val()
    };

    const response = await asyngAjaxSend(
      'config/point/form_edit_save',
      data
    );

    if (response.status) {

      showAlert(
        response.msg || 'EDITADO CORRECTAMENTE',
        'success'
      );

      await point_panel_load();

      return;
    }

    showAlert(
      response.msg ||
      response.error ||
      'NO SE PUDO MODIFICAR',
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

async function point_delete_open(id, name) {
  try {
    const data = {
      id: id,
      name: name,
      type: 'point'
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
      id: 'panel_expedition_point',
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
async function point_delete_save(id) {
  try {
    const data = {
      id: id
    };

    const response = await asyngAjaxSend(
      'config/point/delete_save',
      data
    );

    if (response.status) {
      await point_panel_load();
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