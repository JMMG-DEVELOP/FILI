$(document).ready(async function () {

  auto_cut_verify();
  await panels_load();


});


async function panels_load() {

  await printers_panel_load();
}