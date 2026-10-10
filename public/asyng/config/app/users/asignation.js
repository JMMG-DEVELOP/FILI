$(document).on('click', '#sucursal_asignation_form_new_open', async function (e) {
  e.preventDefault();
  await sucursal_asignation_form_new_open();
});

$(document).on('click', '#sucursal_asignation_form_new_save', async function (e) {
  e.preventDefault();
  await sucursal_asignation_form_new_save();
});

$(document).on(
  'click',
  '.sucursal_asignation_form_edit_open',
  async function (e) {
    e.preventDefault();

    const id = $(this).data('id');

    if (!id) {
      showAlert(
        'NO SE RECIBIÓ EL ID',
        'warning'
      );
      return;
    }

    await sucursal_asignation_form_edit_open(id);
  }
);
$(document).on(
  'click',
  '#sucursal_asignation_form_edit_save',
  async function (e) {
    e.preventDefault();

    await sucursal_asignation_form_edit_save();
  }
);
$(document).on('click', '.sucursal_asignation_form_delete_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  const name = $(this).data('name');
  await sucursal_asignation_delete_open(id, name);
});
$(document).on(
  'click',
  '#delete_sucursal_asignation',
  async function (e) {
    e.preventDefault();

    const id = $(this).data('id');

    if (!id) {
      showAlert(
        'NO SE RECIBIÓ EL ID',
        'warning'
      );
      return;
    }

    await sucursal_asignation_delete_save(id);
  }
);


$(document).on('click', '.sucursal_asignation_delete_close', async function (e) {
  e.preventDefault();
  await sucursal_asignation_panel_load();
});

$(document).on('click', '#sucursal_asignation_form_close', async function (e) {
  e.preventDefault();
  await sucursal_asignation_panel_load();
});

