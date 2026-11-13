$(document).on('keydown', '#shopp_code', function (e) {

  if (e.key === 'Enter' && !e.repeat) {
    e.preventDefault();
    const code = $(this).val().trim();
    shopp_win(code)
  }

});

async function shopp_win(code) {

  // if (shopp_validation(code)) {
  //   await window_view(
  //     'products/products/shopp/shopp_win',
  //     'shopp_win',
  //     {}
  //   );
  // }

}

async function shopp_validation(code) {
  return false
}