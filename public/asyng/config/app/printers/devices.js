

$(document).on('click', '.devices_form_edit_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await devices_form_edit_open(id);
});
$(document).on('click', '#devices_form_edit_save', async function (e) {
  e.preventDefault();
  await devices_form_edit_save();
});
$(document).on('click', '.devices_form_delete_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  const name = $(this).data('name');
  await devices_delete_open(id, name);
});
$(document).on('click', '#delete_devices', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await devices_delete_save(id);
});


$(document).on('click', '.devices_delete_close', async function (e) {
  e.preventDefault();
  await devices_panel_load();
});

$(document).on('click', '#devices_form_close', async function (e) {
  e.preventDefault();
  await devices_panel_load();
});

