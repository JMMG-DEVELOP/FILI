$(document).on('click', '.product_history', function (e) {

  e.preventDefault();

  const code = $(this).data('code');

  view_panel_load(code);
});
async function view_panel_load(code) {

  await window_view(
    'products/products/home/view/panel_load',
    'home_panel',
    {
      code: code
    }
  );

}
