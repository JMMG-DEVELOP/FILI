
async function ticket(sale_id) {
  return imprimirVenta(sale_id, 1);
}

async function factura(sale_id) {
  return imprimirInvoiceTwo(sale_id, 2);
}


async function imprimirVenta(sale_id, type) {
  const response = await asyngAjaxSend(
    'box/prints/print_data',
    {
      sale_id: sale_id,
      type: type
    }
  );

  if (!response || response.status !== true) {
    throw new Error(
      response?.error || 'No se pudieron obtener los datos de impresión'
    );
  }

  const printer = response.printer;

  if (!printer || !printer.printer_system_name) {
    throw new Error('No se recibió el nombre de la impresora');
  }

  if (!Array.isArray(response.print_data) ||
    response.print_data.length === 0) {
    throw new Error('El servidor no devolvió datos para imprimir');
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
      encoding: printer.charset_code || 'UTF-8'
    }
  );

  await qz.print(config, response.print_data);

  console.log('Impresión enviada a QZ Tray');

  return response;
}


async function imprimirPDF(sale_id, type) {
  // Abrir la ventana inmediatamente para reducir bloqueos de pop-ups.
  const pdfWindow = window.open('about:blank', '_blank');

  try {
    const response = await asyngAjaxSend(
      'box/prints/print_pdf',
      {
        sale_id: sale_id,
        type: type
      }
    );

    if (!response || response.status !== true || !response.pdf) {
      throw new Error(
        response?.error || 'El servidor no devolvió el PDF'
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

    if (pdfWindow && !pdfWindow.closed) {
      pdfWindow.location.href = url;
    } else {
      window.open(url, '_blank');
    }

    // Revocar la URL después de un tiempo para liberar memoria.
    setTimeout(() => URL.revokeObjectURL(url), 60000);

  } catch (error) {
    if (pdfWindow && !pdfWindow.closed) {
      pdfWindow.close();
    }

    throw error;
  }
}



async function imprimirInvoiceTwo(sale_id, type = 2) {
  try {
    const response = await asyngAjaxSend(
      'box/prints/print_invoice_two',
      {
        sale_id: sale_id,
        type: type
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



async function invoicePdfTwo() {

}

