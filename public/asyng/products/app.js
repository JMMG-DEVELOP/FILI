
$(document).ready(async function () {
  await panels_load();
});

async function panels_load() {

  await products_panel_load();
  await provider_panel_load()
}

async function products_panel_load() {

  await window_view(
    'products/panels/products',
    'products_panel',
    {},
    async function (response) {
      await home_panel_load();
      await shopp_panel_load();
    }
  );

}
