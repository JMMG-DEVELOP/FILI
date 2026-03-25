$(document).on('change', '#select_sucursal', function () {

  let sucursal = $(this).val();
  let user = $('#user_id').val();

  expedition_point_select(sucursal, user);

});

$(document).on('change', '#receipt_type', function () {

  expedition_point_values()
});