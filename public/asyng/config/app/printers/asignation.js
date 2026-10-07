$(document).on('click', '#asignation_form_new_open', async function (e) {
  e.preventDefault();
  await asignation_form_new_open();
});

$(document).on('click', '#asignation_form_new_save', async function (e) {
  e.preventDefault();
  await asignation_form_new_save();
});

$(document).on('click', '.asignation_form_edit_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await asignation_form_edit_open(id);
});
$(document).on('click', '#asignation_form_edit_save', async function (e) {
  e.preventDefault();
  await asignation_form_edit_save();
});
$(document).on('click', '.asignation_form_delete_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  const name = $(this).data('name');
  await asignation_delete_open(id, name);
});
$(document).on('click', '#delete_asignation', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await asignation_delete_save(id);
});


$(document).on('click', '.asignation_delete_close', async function (e) {
  e.preventDefault();
  await asignation_panel_load();
});

$(document).on('click', '#asignation_form_close', async function (e) {
  e.preventDefault();
  await asignation_panel_load();
});

