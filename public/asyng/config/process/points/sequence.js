async function sequence_panel_load() {
  try {
    const response = await asyngAjaxSend(
      'config/sequence/panel_load'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo cargar el panel',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_sequence',
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


async function sequence_form_new_open() {
  try {
    const response = await asyngAjaxSend(
      'config/sequence/form_new_open'
    );

    if (!response.status) {
      showAlert(
        response.error || 'No se pudo abrir el formulario.',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_sequence',
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

async function sequence_form_new_save() {
  try {
    if (!validateForm('#sequence')) {
      return;
    }
    const data = {
      values: asyngFormData('#form_sequence')
    };

    const response = await asyngAjaxSend(
      'config/sequence/form_new_save',
      data
    );

    if (response.status) {
      showAlert(
        response.msg || ' GUARDADA CORRECTAMENTE',
        'success'
      );

      await sequence_panel_load();
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

async function sequence_form_edit_open(id, type) {

  try {

    const data = {
      id: id,
      type: type
    };

    const response = await asyngAjaxSend(
      'config/sequence/form_edit_open',
      data
    );

    if (!response.status) {

      showAlert(
        response.msg ||
        response.error ||
        'NO SE PUDO ABRIR EL FORMULARIO',
        'warning'
      );

      return;
    }

    asyng_show_view({
      id: 'panel_sequence',
      html: response.html,
      effect: 'fade'
    });

  } catch (error) {

    console.error(error);

    showAlert(
      'ERROR DE COMUNICACION CON EL SERVIDOR',
      'danger'
    );

  }
}


$(document).on(
  'click',
  '.invoice_sequence_edit_open',
  async function (e) {

    e.preventDefault();

    const id = $(this).data('id');
    const type = $(this).data('type');

    await sequence_form_edit_open(
      id,
      type
    );

  }
);


$(document).on(
  'click',
  '.document_sequence_edit_open',
  async function (e) {

    e.preventDefault();

    const id = $(this).data('id');
    const type = $(this).data('type');

    await sequence_form_edit_open(
      id,
      type
    );

  }
);

$(document).on(
  'click',
  '#sequence_form_edit_save',
  async function (e) {

    e.preventDefault();

    if (!validateForm('#form_sequence')) {
      return;
    }

    try {

      const data = {
        values: asyngFormData('#form_sequence'),
        id: $('#sequence_id').val(),
        type: $('#sequence_type').val()
      };

      const response = await asyngAjaxSend(
        'config/sequence/form_edit_save',
        data
      );

      if (!response.status) {

        showAlert(
          response.msg ||
          response.error ||
          'NO SE PUDO MODIFICAR LA SECUENCIA',
          'warning'
        );

        return;
      }

      showAlert(
        response.msg ||
        'SECUENCIA MODIFICADA CORRECTAMENTE',
        'success'
      );

      const panelResponse = await asyngAjaxSend(
        'config/sequence/panel_load'
      );

      if (panelResponse.status) {

        asyng_show_view({
          id: 'panel_sequence',
          html: panelResponse.html,
          effect: 'fade'
        });

      }

    } catch (error) {

      console.error(error);

      showAlert(
        'ERROR DE COMUNICACION CON EL SERVIDOR',
        'danger'
      );

    }

  }
);

async function sequence_delete_open(
  id,
  name,
  type,
  sequenceType
) {

  try {

    const data = {
      id: id,
      name: name,
      type: type,
      sequence_type: sequenceType
    };

    const response = await asyngAjaxSend(
      'config/sequence/delete_open',
      data
    );

    if (!response.status) {

      showAlert(
        response.error ||
        response.msg ||
        'NO SE PUDO ABRIR EL FORMULARIO',
        'warning'
      );

      return;
    }

    asyng_show_view({
      id: 'panel_sequence',
      html: response.html,
      effect: 'fade'
    });

  } catch (err) {

    console.error(err);

    showAlert(
      'ERROR DE COMUNICACION CON EL SERVIDOR',
      'danger'
    );
  }
}

async function sequence_delete_save(id) {
  try {
    const data = {
      id: id
    };

    const response = await asyngAjaxSend(
      'config/point/delete_save',
      data
    );

    if (response.status) {
      await sequence_panel_load();
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