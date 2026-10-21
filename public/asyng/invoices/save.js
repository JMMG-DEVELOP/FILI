
$(document).on('click', '#invoice_form_create_save', async function (e) {
  e.preventDefault();

  const button = $(this);

  // Cliente
  const customerId = Number($('#customer_id').val() || 0);

  // Punto de expedición
  const sucursalId = Number($('#select_sucursal').val() || 0);
  const expeditionPointId = Number($('#expedition_point_id').val() || 0);
  const sequenceId = Number($('#sequence_id').val() || 0);
  const sequenceNumber = Number($('#sequence_number').val() || 0);
  const invoiceNumber = String($('#invoice_number').val() || '').trim();

  // Ventas agregadas a la factura
  const sales = [];

  $('#invoice_sales_selected .invoice-sale-item').each(function () {
    const id = Number($(this).attr('data-sale-id') || 0);

    if (id > 0) {
      sales.push(id);
    }
  });

  // Validaciones básicas del formulario
  if (customerId <= 0) {
    showAlert('DEBE SELECCIONAR UN CLIENTE', 'warning');
    return;
  }

  if (sucursalId <= 0 || expeditionPointId <= 0) {
    showAlert('DEBE SELECCIONAR UN PUNTO DE EXPEDICIÓN VÁLIDO', 'warning');
    return;
  }

  if (!invoiceNumber || sequenceId <= 0 || sequenceNumber <= 0) {
    showAlert('LOS DATOS DE LA FACTURA ESTÁN INCOMPLETOS', 'warning');
    return;
  }

  if (sales.length === 0) {
    showAlert('DEBE AGREGAR AL MENOS UNA VENTA', 'warning');
    return;
  }

  // Evitar doble envío mientras se guarda
  if (button.data('saving')) {
    return;
  }

  button.data('saving', true);
  button.prop('disabled', true);

  try {
    const response = await asyngAjaxSend(
      'invoice/create/save_invoice',
      {
        customer_id: customerId,
        select_sucursal: sucursalId,
        expedition_point_id: expeditionPointId,
        sequence_id: sequenceId,
        sequence_number: sequenceNumber,
        invoice_number: invoiceNumber,
        sales: sales
      }
    );

    if (!response || response.status !== true) {
      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDO GUARDAR LA FACTURA',
        'warning'
      );

      return;
    }

    showAlert(
      response.msg || 'FACTURA GUARDADA CORRECTAMENTE',
      'success'
    );

    console.log('FACTURA GUARDADA:', response.data);

    // Limpiar las ventas agregadas
    $('#invoice_sales_selected').empty();

    // Actualizar los totales visuales
    $('#total_invoice').val('');
    $('#items_invoice').val('');

    /*
     * No limpiar automáticamente el cliente ni cambiar la secuencia
     * hasta confirmar cómo se recarga el formulario de facturación.
     */

    invoice_form_create_open()


    // IMPREMIR FACTURA

  } catch (error) {
    console.error('ERROR AL GUARDAR LA FACTURA:', error);

    showAlert(
      'ERROR DE COMUNICACIÓN AL GUARDAR LA FACTURA',
      'danger'
    );

  } finally {
    button.data('saving', false);
    button.prop('disabled', false);
  }
});
