async function imprimirTicket(sale_id, type) {
  try {
    const response = await asyngAjaxSend(
      'prints/ticket',
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

    if (!Array.isArray(response.print_data) || response.print_data.length === 0) {
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

    return {
      status: true,
      method: 'qz',
      response: response
    };

  } catch (error) {
    console.error('Error al imprimir con QZ Tray:', error);

    showAlert(
      'No se pudo imprimir con la impresora. Se generará el PDF de respaldo.',
      'warning'
    );

    const pdfGenerated = await generateTicketPdf(sale_id, type);

    if (!pdfGenerated) {
      throw new Error('Falló la impresión y tampoco se pudo generar el PDF');
    }

    return {
      status: true,
      method: 'pdf'
    };
  }
}

async function generateTicketPdf(saleId, type) {
  const response = await asyngAjaxSend(
    'prints/ticket_pdf',
    {
      sale_id: saleId,
      type: type
    },
    true
  );

  if (!response || response.status !== true || !response.pdf) {
    showAlert(
      response?.error || 'NO SE PUDO GENERAR EL PDF',
      'error'
    );

    return false;
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
    link.download = response.filename || 'ticket.pdf';
    document.body.appendChild(link);
    link.click();
    link.remove();
  }

  // Liberar la URL después de darle tiempo al navegador para abrir el PDF.
  setTimeout(() => URL.revokeObjectURL(url), 60000);

  return true;
}


