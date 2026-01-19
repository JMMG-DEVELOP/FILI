function brand_add_open() {
  $.post(
    BASE_URL + '/products/brands/brand_open',
    {
      [csrfName]: csrfHash
    },
    function (response) {
      if (response.status) {
        open_hide('#brands_form', '#brands_panel', response.form)

      }
      csrfHash = response.csrfHash;
    },
    'json'
  );
}

$(document).on('click', '.brand_cancel', function (e) {
  e.preventDefault();
  cancel_open('#brands_form', '#brands_panel');
});

$(document).on('click', '.brand_add', function (e) {
  e.preventDefault();
  brand_add_open();
});


