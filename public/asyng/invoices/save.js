
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

    // Obtener el ID de la factura guardada
    const invoiceId = Number(response.data?.invoice_id || 0);

    if (invoiceId <= 0) {
      showAlert(
        'FACTURA GUARDADA, PERO NO SE PUDO OBTENER SU ID PARA IMPRIMIR',
        'warning'
      );
      return;
    }

    // Limpiar las ventas agregadas
    $('#invoice_sales_selected').empty();

    // Actualizar los totales visuales
    $('#total_invoice').val('');
    $('#items_invoice').val('');

    // Imprimir la factura guardada
    await generate_invoice_print(invoiceId);

    // Recargar el formulario después de solicitar la impresión
    invoice_form_create_open();

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


$(document).on('click', '#invoice_form_create_save', async function (e) {
  e.preventDefault();

  const button = $(this);

  if (button.data('saving')) {
    return;
  }

  // Cliente
  const customerId = Number($('#customer_id').val() || 0);

  // Sucursal y punto de expedición
  const sucursalId = Number($('#select_sucursal').val() || 0);
  const expeditionPointId = Number($('#expedition_point_id').val() || 0);
  const sequenceId = Number($('#sequence_id').val() || 0);
  const sequenceNumber = Number($('#sequence_number').val() || 0);
  const invoiceNumber = String($('#invoice_number').val() || '').trim();

  // Ventas agregadas
  const sales = [];

  $('#invoice_sales_selected .invoice-sale-item').each(function () {
    const id = Number($(this).attr('data-sale-id') || 0);

    if (id > 0) {
      sales.push(id);
    }
  });

  // Validaciones
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

  /*
   * Abrir la ventana directamente desde el clic.
   * Así Chrome no debería bloquear el PDF de respaldo.
   */
  let printWindow = null;

  try {
    printWindow = window.open('', '_blank');

    if (printWindow) {
      printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head><title>Preparando factura</title></head>
        <body style="font-family:Arial;text-align:center;padding:40px">
          <h3>Preparando factura...</h3>
          <p>Por favor, espera.</p>
        </body>
        </html>
      `);
      printWindow.document.close();
    }

    button.data('saving', true);
    button.prop('disabled', true);

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
      if (printWindow && !printWindow.closed) {
        printWindow.close();
      }

      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDO GUARDAR LA FACTURA',
        'warning'
      );

      return;
    }

    console.log('FACTURA GUARDADA:', response.data);

    const invoiceId = Number(response.data?.invoice_id || 0);

    if (invoiceId <= 0) {
      if (printWindow && !printWindow.closed) {
        printWindow.close();
      }

      showAlert(
        'FACTURA GUARDADA, PERO NO SE RECIBIÓ SU ID',
        'warning'
      );

      return;
    }

    // Limpiar la selección después de guardar correctamente.
    $('#invoice_sales_selected').empty();
    $('#total_invoice').val('');
    $('#items_invoice').val('');

    showAlert(
      response.msg || 'FACTURA GUARDADA CORRECTAMENTE',
      'success'
    );

    // Imprimir la factura guardada.
    await generate_invoice_print(invoiceId, printWindow);

    // Volver a cargar el formulario.
    invoice_form_create_open();

  } catch (error) {
    console.error('ERROR AL GUARDAR O IMPRIMIR FACTURA:', error);

    if (printWindow && !printWindow.closed) {
      printWindow.close();
    }

    showAlert(
      'ERROR DE COMUNICACIÓN AL GUARDAR O IMPRIMIR LA FACTURA',
      'danger'
    );

  } finally {
    button.data('saving', false);
    button.prop('disabled', false);
  }
});


async function generate_invoice_print(invoiceId, printWindow = null) {
  let pdfUrl = null;

  try {
    const response = await asyngAjaxSend(
      'prints/generate_invoice_print',
      {
        invoice_id: invoiceId
      },
      true
    );

    if (!response || response.status !== true || !response.pdf) {
      console.error('RESPUESTA DE IMPRESIÓN:', response);

      if (printWindow && !printWindow.closed) {
        printWindow.close();
      }

      showAlert(
        response?.error ||
        response?.detail ||
        response?.message ||
        'NO SE PUDO GENERAR EL PDF DE LA FACTURA',
        'danger'
      );

      return false;
    }

    const printer = response.printer || {};

    /*
     * Intentar QZ Tray primero.
     * Para la factura A4 se envía el PDF como contenido pixel.
     */
    if (
      typeof qz !== 'undefined' &&
      printer.printer_system_name &&
      typeof qz.print === 'function'
    ) {
      try {
        if (!qz.websocket.isActive()) {
          await qz.websocket.connect();
        }

        const config = qz.configs.create(
          printer.printer_system_name
        );

        await qz.print(config, [
          {
            type: 'pixel',
            format: 'pdf',
            flavor: 'base64',
            data: response.pdf
          }
        ]);

        // QZ aceptó el trabajo de impresión.
        if (printWindow && !printWindow.closed) {
          printWindow.close();
        }

        showAlert('FACTURA ENVIADA A LA IMPRESORA', 'success');

        return true;

      } catch (qzError) {
        console.error('ERROR DE IMPRESIÓN QZ TRAY:', qzError);

        /*
         * QZ puede fallar después de enviar el trabajo.
         * Si el estado es incierto, evita enviar automáticamente
         * el mismo documento otra vez y generar una copia duplicada.
         */
        showAlert(
          'QZ TRAY NO CONFIRMÓ LA IMPRESIÓN. VERIFICA LA IMPRESORA ANTES DE REINTENTAR.',
          'warning'
        );

        if (printWindow && !printWindow.closed) {
          printWindow.close();
        }

        return false;
      }
    }

    /*
     * Alternativa: abrir el PDF en el navegador.
     * Se usa cuando QZ Tray no está disponible.
     */
    const binary = atob(response.pdf);
    const bytes = new Uint8Array(binary.length);

    for (let i = 0; i < binary.length; i++) {
      bytes[i] = binary.charCodeAt(i);
    }

    const pdfBlob = new Blob(
      [bytes],
      { type: 'application/pdf' }
    );

    pdfUrl = URL.createObjectURL(pdfBlob);

    if (!printWindow || printWindow.closed) {
      printWindow = window.open('', '_blank');
    }

    if (!printWindow) {
      showAlert(
        'EL PDF SE GENERÓ, PERO EL NAVEGADOR BLOQUEÓ LA VENTANA. PERMITE LAS VENTANAS EMERGENTES.',
        'warning'
      );

      return false;
    }

    printWindow.location.href = pdfUrl;

    // El visor PDF puede tardar en cargar.
    // Abrir el documento permite imprimirlo manualmente.
    showAlert(
      'PDF GENERADO. SI NO APARECE LA IMPRESIÓN AUTOMÁTICA, IMPRIME DESDE EL VISOR PDF.',
      'success'
    );

    // No revocar la URL inmediatamente: el visor necesita cargarla.
    setTimeout(() => {
      URL.revokeObjectURL(pdfUrl);
    }, 60000);

    return true;

  } catch (error) {
    console.error('ERROR AL GENERAR O IMPRIMIR LA FACTURA:', error);

    if (printWindow && !printWindow.closed) {
      printWindow.close();
    }

    showAlert(
      'NO SE PUDO ABRIR O IMPRIMIR LA FACTURA. REVISA LA CONSOLA.',
      'danger'
    );

    return false;
  }
}
