
$(document).ready(async function () {
  await panels_load();
});

async function panels_load() {
  await Promise.all([
    home_panel_load(),
    months_panel_load(),

  ]);
}
