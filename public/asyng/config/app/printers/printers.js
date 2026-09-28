$(document).on('click', '#printers_form_new_open', async function (e) {
  e.preventDefault();
  await form_new_open();
});

$(document).on('click', '#printers_form_new_save', async function (e) {
  e.preventDefault();
  await form_new_save();
});

$(document).on('click', '.printers_form_edit_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await form_edit_open(id);
});
$(document).on('click', '#printers_form_edit_save', async function (e) {
  e.preventDefault();
  await form_edit_save();
});
$(document).on('click', '.printers_form_delete_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  const name = $(this).data('name');
  await delete_open(id, name);
});
$(document).on('click', '#delete_printer', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await delete_save(id);
});


$(document).on('click', '.delete_close', async function (e) {
  e.preventDefault();
  await printers_panel_load();
});

$(document).on('click', '#printers_form_close', async function (e) {
  e.preventDefault();
  await printers_panel_load();
});
function auto_cut_verify() {
  $(document).on('change', '#auto_cut', function () {
    if ($(this).is(':checked')) {
      $('#auto_cut_text').text('HABILITADO');
    } else {
      $('#auto_cut_text').text('NO HABILITADO');
    }
  });
}
