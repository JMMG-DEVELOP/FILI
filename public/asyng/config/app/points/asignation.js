$(document).on('click', '#point_asignation_form_new_open', async function (e) {
  e.preventDefault();
  await point_asignation_form_new_open();
});

$(document).on('click', '#point_asignation_form_new_save', async function (e) {
  e.preventDefault();
  await point_asignation_form_new_save();
});

$(document).on('click', '.point_asignation_form_edit_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await point_asignation_form_edit_open(id);
});
$(document).on('click', '#point_asignation_form_edit_save', async function (e) {
  e.preventDefault();
  await point_asignation_form_edit_save();
});
$(document).on('click', '.point_asignation_form_delete_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  const name = $(this).data('name');
  await point_asignation_delete_open(id, name);
});
$(document).on('click', '#delete_point_asignation', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await point_asignation_delete_save(id);
});


$(document).on('click', '.point_asignation_delete_close', async function (e) {
  e.preventDefault();
  await point_asignation_panel_load();
});

$(document).on('click', '#point_asignation_form_close', async function (e) {
  e.preventDefault();
  await point_asignation_panel_load();
});

