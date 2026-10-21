
async function home_panel_load() {
  try {
    const response = await asyngAjaxSend(
      'customer/home/panel_load'
    );

    if (!response || response.status !== true) {
      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDO CARGAR EL PANEL DE CLIENTES',
        'warning'
      );

      return;
    }

    asyng_show_view({
      id: 'panel_home',
      html: response.html,
      effect: 'fade',
      callback: function () {
        home_init_table();
      }
    });

  } catch (error) {
    console.error(
      'ERROR AL CARGAR EL PANEL DE CLIENTES:',
      error
    );

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );
  }
}
// Abrir Formulario para Nuevo Registro
$(document).on('click', '#customer_form_new_open', async function (e) {
  e.preventDefault();
  await customer_form_new_open();
});

async function customer_form_new_open() {
  try {
    const response = await asyngAjaxSend(
      'customer/home/form_new_open'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_home',
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
// End
// Abrir Formulario para Nuevo Registro

// Guardar Nuevo Registro
$(document).on('click', '#customer_form_new_save', async function (e) {
  e.preventDefault();
  await customer_form_new_save();
});
async function customer_form_new_save() {
  try {
    if (!validateForm('#form_customer')) {
      showAlert('COMPLETAR CORRECTAMENTE LOS DATOS', 'warning')
      return;
    }
    const data = {
      values: asyngFormData('#form_customer')
    };

    const response = await asyngAjaxSend(
      'customer/home/form_new_save',
      data
    );

    if (response.status) {
      showAlert(
        response.msg || ' GUARDADA CORRECTAMENTE',
        'success'
      );

      await home_panel_load();
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

// End Guardar Nuevo Registro
// Abrir Editar Registro
$(document).on('click', '.customer_edit', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await customer_form_edit_open(id);
});
async function customer_form_edit_open(id) {
  try {
    const data = {
      id: id
    }
    const response = await asyngAjaxSend(
      'customer/home/form_edit_open', data
    );

    if (!response.status) {
      showAlert(
        response.msg || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }
    asyng_show_view({
      id: 'panel_home',
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
// gUARDAR CAMBIOS Editar Registro
$(document).on('click', '#customer_form_edit_save', async function (e) {
  e.preventDefault();
  e.preventDefault();
  const id = $(this).data('id');
  await customer_form_edit_save(id);
});
async function customer_form_edit_save(id) {
  try {
    if (!validateForm('#form_customer')) {
      return;
    }

    const values = asyngFormData('#form_customer');
    const data = {
      values: values,
      id: $('#customer_id').val()
    };

    const response = await asyngAjaxSend(
      'customer/home/form_edit_save',
      data
    );

    if (response.status) {
      await home_panel_load();
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

// Abrir Modal de Eliminar
$(document).on('click', '.customer_delete', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  const name = $(this).data('name');
  await customer_delete_open(id, name);
});
async function customer_delete_open(id, name) {
  try {
    const data = {
      id: id,
      name: name,
      type: 'customer'
    };
    const response = await asyngAjaxSend(
      'customer/delete_open', data
    );

    if (!response.status) {
      showAlert(
        response.error || 'NO SE PUDO ABRIR EL FORMULARIO',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_home',
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
// Borrar CLIENTE
$(document).on('click', '#delete_customer', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await customer_delete_save(id);
});
async function customer_delete_save(id) {
  try {
    const response = await asyngAjaxSend(
      'customer/home/delete_save',
      { id: id }
    );

    if (!response.status) {
      showAlert(
        response.msg || 'NO SE PUEDE ELIMINAR EL CLIENTE PORQUE TIENE HISTORIAL.',
        'warning'
      );
      return;
    }

    showAlert(
      response.msg || 'CLIENTE ELIMINADO CORRECTAMENTE',
      'success'
    );

    await home_panel_load();

  } catch (err) {
    console.error('Error al eliminar cliente:', err);

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );
  }
}

// Cierre de Formulario
$(document).on('click', '#customer_form_close', async function (e) {
  e.preventDefault();
  await home_panel_load();
});
$(document).on('click', '.customer_form_delete_close', async function (e) {
  e.preventDefault();
  await home_panel_load();
});