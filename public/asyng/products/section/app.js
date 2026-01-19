function section_add_open() {
  $.post(
    BASE_URL + '/products/section/section_open',
    {
      [csrfName]: csrfHash
    },
    function (response) {
      if (response.status) {
        open_hide('#section_form', '#section_panel', response.form)

      }
      csrfHash = response.csrfHash;
    },
    'json'
  );
}

$(document).on('click', '.section_cancel', function (e) {
  e.preventDefault();
  cancel_open('#section_form', '#section_panel');
});

$(document).on('click', '.section_add', function (e) {
  e.preventDefault();
  section_add_open();

});


