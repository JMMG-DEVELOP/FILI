async function point_asignation_panel_load() {
  try {
    const response = await asyngAjaxSend(
      'config/point_asignation/panel_load'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo cargar el panel',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_point_asignation',
      html: response.html,
      effect: 'fade',

    });

  } catch (err) {
    showAlert(
      'Error de comunicación con el servidor',
      'danger'
    );
  }
}

// function papers_table_init() {

//   const table = $('#table_paper');

//   if (!table.length) {

//     console.warn(
//       'No se encontró dentro de #panel_printers'
//     );

//     return;
//   }

//   AppTable({
//     table: '#table_paper',
//     pageLength: 10,
//     serverSide: false,
//     processing: false,
//     responsive: true,
//     searchDelay: 300,
//     exportButtons: false
//   });
// }

async function point_asignation_form_new_open() {
  try {
    const response = await asyngAjaxSend(
      'config/point_asignation/form_new_open'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_point_asignation',
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

async function point_asignation_form_new_save() {
  try {
    if (!validateForm('#form_point_asignation')) {
      return;
    }
    const data = {
      values: asyngFormData('#form_point_asignation')
    };

    const response = await asyngAjaxSend(
      'config/point_asignation/form_new_save',
      data
    );

    if (response.status) {
      showAlert(
        response.msg || ' GUARDADA CORRECTAMENTE',
        'success'
      );

      await point_asignation_panel_load();
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

async function point_asignation_form_edit_open(id) {
  try {
    const data = {
      id: id
    }
    const response = await asyngAjaxSend(
      'config/point_asignation/form_edit_open', data
    );

    if (!response.status) {
      showAlert(
        response.msg || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }
    asyng_show_view({
      id: 'panel_point_asignation',
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
async function point_asignation_form_edit_save() {
  try {

    if (!validateForm('#form_point_asignation')) {
      return;
    }

    const values = asyngFormData('#form_point_asignation');

    const data = {
      values: values,
      id: $('#point_asignation_id').val()
    };

    const response = await asyngAjaxSend(
      'config/point_asignation/form_edit_save',
      data
    );

    if (response.status) {

      await point_asignation_panel_load();

      showAlert(
        response.msg || 'EDITADO CORRECTAMENTE',
        'success'
      );

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
async function point_asignation_delete_open(id, name) {
  try {
    const data = {
      id: id,
      name: name,
      type: 'point_asignation'
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
      id: 'panel_point_asignation',
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
async function point_asignation_delete_save(id) {
  try {
    const data = {
      id: id
    };

    const response = await asyngAjaxSend(
      'config/point_asignation/delete_save',
      data
    );

    if (response.status) {
      await point_asignation_panel_load();
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