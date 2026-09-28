$(document).on(
  asyngMoneyMask()
);

$(document).on('click', '#btn_box_close', function () {
  box_close_save();
});

$(document).on('click', '#btn_box_finish', function () {
  close();
});
$(document).on('click', '#btn_box_open', function () {
  open_box();
});