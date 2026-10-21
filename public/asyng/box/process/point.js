function expedition_point_values() {
  const sucursal = $('#select_sucursal').val();

  expedition_point_select(sucursal);
}

async function expedition_point_select(sucursal) {

  const receipt_type = parseInt(
    $('#receipt_type').val()
  );

  try {

    const response = await asyngAjaxSend(
      'box/process/expedition_point_select',
      {
        sucursal: sucursal
      }
    );

    if (
      response.status &&
      Array.isArray(response.values) &&
      response.values.length > 0
    ) {

      const point = response.values.find(function (value) {
        return String(value.sucursal) === String(sucursal);
      });

      if (!point) {
        $('#invoice_number').val('');
        $('#expedition_point_id').val('');
        $('#sequence_id').val('');
        $('#sequence_number').val('');

        showAlert(
          'No se encontró punto de expedición para esta sucursal',
          'warning'
        );

        return;
      }

      if (receipt_type === 1) {

        $('#invoice_number').val(point.document_number);
        $('#expedition_point_id').val(point.expedition_point);
        $('#sequence_id').val(point.document_sequence_id);
        $('#sequence_number').val(point.document_next_number);
      }

      if (receipt_type === 2) {

        $('#invoice_number').val(point.invoice_number);
        $('#expedition_point_id').val(point.expedition_point);
        $('#sequence_id').val(point.invoice_sequence_id);
        $('#sequence_number').val(point.invoice_next_number);
      }

    } else {

      $('#invoice_number').val('');
      $('#expedition_point_id').val('');
      $('#sequence_id').val('');
      $('#sequence_number').val('');

      showAlert(
        'No se encontró punto de expedición para este dispositivo',
        'warning'
      );
    }

  } catch (err) {

    console.error(err);

    showAlert(
      'Error de comunicación con el servidor expedition_point_select',
      'danger'
    );
  }
}




