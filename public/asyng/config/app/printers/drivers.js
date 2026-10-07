$(document).on('click', '#drivers_form_new_open', async function (e) {
  e.preventDefault();
  await drivers_form_new_open();
});

$(document).on('click', '#drivers_form_new_save', async function (e) {
  e.preventDefault();
  await drivers_form_new_save();
});

$(document).on('click', '.drivers_form_edit_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await drivers_form_edit_open(id);
});
$(document).on('click', '#drivers_form_edit_save', async function (e) {
  e.preventDefault();
  await drivers_form_edit_save();
});
$(document).on('click', '.drivers_form_delete_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  const name = $(this).data('name');
  await drivers_delete_open(id, name);
});
$(document).on('click', '#delete_driver', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await drivers_delete_save(id);
});


$(document).on('click', '.driver_delete_close', async function (e) {
  e.preventDefault();
  await drivers_panel_load();
});

$(document).on('click', '#drivers_form_close', async function (e) {
  e.preventDefault();
  await drivers_panel_load();
});

