function expedition_point_values() {
  let sucursal = $('#select_sucursal').val();
  let user = $('#user_id').val();

  expedition_point_select(sucursal, user);
}
async function expedition_point_select(sucursal, user) {

  let receipt_type = parseInt($('#receipt_type').val());

  try {
    const response = await asyngAjaxSend(
      'box/process/expedition_point_select',
      { sucursal: sucursal, user: user }
    );

    if (response.status && response.values) {

      if (receipt_type === 1) {
        $('#invoice_number').val(response.values.document_number);
        $('#expedition_point_id').val(response.values.expedition_point);
        $('#sequence_id').val(response.values.document_sequence_id);
      }

      if (receipt_type === 2) {
        $('#invoice_number').val(response.values.invoice_number);
        $('#expedition_point_id').val(response.values.expedition_point);
        $('#sequence_id').val(response.values.invoice_sequence_id);
      }

    } else {
      showAlert('No se encontró punto de expedición', 'warning');
    }

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor expedition_point_select', 'danger');
  }
}


