$(document).on('click', '.shopp_winup', function (e) {
  e.preventDefault();
  $('#shopp_code').focus();
});
async function shopp_panel_load() {

  await window_view(
    'products/products/shopp/shopp',
    'shopp_panel',
    {}
  );
}
$(document).on('input', '#shopp_code', function () {
  formatCode(this);
});
$(document).on('keydown', '.shopp_search', async function (e) {
  if (e.key === 'Shift' && !e.repeat) {
    const code = $(this).val().trim();
    await window_view(
      'products/products/shopp/shopp_search',
      'shop_search_result',
      { value: code },
      function (response) {
      }
    );
  }
});

$(document).on('click', '.shopp_table_hide', function () {
  asyng_hide_view({
    id: 'shop_search_result',
    effect: 'fade',
    time: 150,
    clear: true
  });
  $('#shopp_code').focus().select();
});
$(document).on('click', '.shopp_product_form_close', function () {
  shopp_panel_load()
});
