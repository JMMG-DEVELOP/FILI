
$(document).ready(async function () {

  auto_cut_verify();

  await panels_load();

});


async function panels_load() {

  await Promise.all([
    printers_panel_load(),
    drivers_panel_load(),
    papers_panel_load(),
    devices_panel_load(),
    category_panel_load(),
    users_panel_load(),
    asignation_panel_load(),
    point_panel_load(),
    sequence_panel_load()

  ]);

}