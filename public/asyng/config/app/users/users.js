$(document).on('click', '#users_form_new_open', async function (e) {
  e.preventDefault();
  await users_form_new_open();
});

$(document).on('click', '#users_form_new_save', async function (e) {
  e.preventDefault();
  await users_form_new_save();
});

$(document).on('click', '.users_form_edit_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await users_form_edit_open(id);
});
$(document).on('click', '#users_form_edit_save', async function (e) {
  e.preventDefault();
  await users_form_edit_save();
});
$(document).on('click', '#users_form_edit_password_save', async function (e) {
  e.preventDefault();
  await users_form_edit_password_save();
});
$(document).on('click', '.users_form_delete_open', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  const name = $(this).data('name');
  await users_delete_open(id, name);
});
$(document).on('click', '#delete_users', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await users_delete_save(id);
});


$(document).on('click', '.users_delete_close', async function (e) {
  e.preventDefault();
  await users_panel_load();
});

$(document).on('click', '#users_form_close', async function (e) {
  e.preventDefault();
  await users_panel_load();
});
$(document).on('click', '#toggle_password', function () {

  const input = $('#password');
  const icon = $(this).find('i');

  if (input.attr('type') === 'password') {

    input.attr('type', 'text');

    icon.removeClass('fa-eye');
    icon.addClass('fa-eye-slash');

    $(this).attr('title', 'Ocultar contraseña');

  } else {

    input.attr('type', 'password');

    icon.removeClass('fa-eye-slash');
    icon.addClass('fa-eye');

    $(this).attr('title', 'Mostrar contraseña');
  }
});

