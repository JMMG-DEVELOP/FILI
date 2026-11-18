$(document).on('click', '#section_form_new_open', async function (e) {
  e.preventDefault();
  await section_form_new_open();
});

$(document).on('click', '#section_form_new_save', async function (e) {
  e.preventDefault();
  await section_form_new_save();
});

$(document).on('click', '.section_form_edit_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await section_form_edit_open(id);
});
$(document).on('click', '#section_form_edit_save', async function (e) {
  e.preventDefault();
  await section_form_edit_save();
});
$(document).on('click', '.section_form_delete_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  const name = $(this).data('name');
  await section_delete_open(id, name);
});
$(document).on('click', '#delete_section', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await section_delete_save(id);
});


$(document).on('click', '.section_delete_close', async function (e) {
  e.preventDefault();
  await section_panel_load();
});

$(document).on('click', '#section_form_close', async function (e) {
  e.preventDefault();
  await section_panel_load();
});

