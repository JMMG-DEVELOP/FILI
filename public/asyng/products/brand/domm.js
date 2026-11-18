$(document).on('click', '#brand_form_new_open', async function (e) {
  e.preventDefault();
  await brand_form_new_open();
});

$(document).on('click', '#brand_form_new_save', async function (e) {
  e.preventDefault();
  await brand_form_new_save();
});

$(document).on('click', '.brand_form_edit_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await brand_form_edit_open(id);
});
$(document).on('click', '#brand_form_edit_save', async function (e) {
  e.preventDefault();
  await brand_form_edit_save();
});
$(document).on('click', '.brand_form_delete_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  const name = $(this).data('name');
  await brand_delete_open(id, name);
});
$(document).on('click', '#delete_brand', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await brand_delete_save(id);
});


$(document).on('click', '.brand_delete_close', async function (e) {
  e.preventDefault();
  await brand_panel_load();
});

$(document).on('click', '#brand_form_close', async function (e) {
  e.preventDefault();
  await brand_panel_load();
});

