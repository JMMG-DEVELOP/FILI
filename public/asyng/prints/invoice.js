async function imprimirInvoice(sale_id, type = 2, receiptType = 2) {
  try {
    const response = await asyngAjaxSend(
      'prints/invoice',
      {
        sale_id: sale_id,
        type: type,
        receipt_type: receiptType

      }
    );

    if (!response || response.status !== true) {
      throw new Error(
        response?.error || 'No se pudo generar la factura'
      );
    }

    if (!response.pdf) {
      throw new Error('El servidor no devolvió el PDF de la factura');
    }

    const printer = response.printer;

    if (!printer || !printer.printer_system_name) {
      throw new Error(
        'No se recibió la impresora configurada para la factura'
      );
    }

    if (typeof qz === 'undefined') {
      throw new Error('QZ Tray no está cargado en la página');
    }

    if (!qz.websocket.isActive()) {
      await qz.websocket.connect();
    }

    const config = qz.configs.create(
      printer.printer_system_name,
      {
        colorType: 'grayscale',
        jobName: response.filename || `Factura_${sale_id}.pdf`
      }
    );

    const printData = [
      {
        type: 'pixel',
        format: 'pdf',
        flavor: 'base64',
        data: response.pdf
      }
    ];

    await qz.print(config, printData);

    console.log(
      'Factura enviada a QZ Tray:',
      printer.printer_system_name
    );

    return response;

  } catch (error) {
    console.error('Error al imprimir la factura:', error);

    showAlert(
      'error',
      error.message || 'No se pudo imprimir la factura'
    );

    throw error;
  }
}

async function generateInvoicePdf(saleId) {
  try {
    const response = await asyngAjaxSend(
      'prints/invoice_pdf',
      {
        sale_id: saleId,
        type: 2
      },
      true
    );

    if (!response || response.status !== true || !response.pdf) {
      throw new Error(
        response?.error || 'NO SE PUDO GENERAR EL PDF'
      );
    }

    const binary = atob(response.pdf);
    const bytes = new Uint8Array(binary.length);

    for (let i = 0; i < binary.length; i++) {
      bytes[i] = binary.charCodeAt(i);
    }

    const blob = new Blob(
      [bytes],
      { type: 'application/pdf' }
    );

    const url = URL.createObjectURL(blob);
    const pdfWindow = window.open(url, '_blank');

    if (!pdfWindow) {
      const link = document.createElement('a');

      link.href = url;
      link.download = response.filename || 'factura.pdf';

      document.body.appendChild(link);
      link.click();
      link.remove();
    }

    setTimeout(() => URL.revokeObjectURL(url), 60000);

    return true;

  } catch (error) {
    console.error('Error al generar PDF de factura:', error);

    showAlert(error.message || 'NO SE PUDO GENERAR EL PDF', 'error');

    return false;
  }
}