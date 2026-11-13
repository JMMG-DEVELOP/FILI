$(document).on('click', '.history_sales_detail_print', async function () {
  const saleId = $(this).data('id');

  if (!saleId) {
    showAlert('error', 'NO SE ENCONTRÓ EL ID DE LA VENTA');
    return;
  }

  try {
    await ticket(saleId);
  } catch (error) {
    console.error('Error al imprimir:', error);
    await imprimirPDF(saleId, 1);
  }
});




async function ticket(sale_id) {
  return imprimirTicket(sale_id, 1);
}

async function factura(sale_id, receiptType = Number($('#receipt_type').val() || 2)) {
  return imprimirInvoice(sale_id, 2, receiptType);
}
async function imprimirPDF(sale_id, type, receiptType = 2) {
  let url;

  if (Number(type) === 1) {
    url = 'prints/ticket_pdf';
  } else if (Number(type) === 2) {
    url = 'prints/invoice_pdf';
  } else {
    throw new Error('Tipo de documento no válido para PDF');
  }

  const response = await asyngAjaxSend(
    url,
    {
      sale_id: sale_id,
      type: Number(type),
      receipt_type: receiptType
    },
    true
  );

  if (!response || response.status !== true || !response.pdf) {
    throw new Error(
      response?.error || 'No se pudo generar el PDF'
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

  const pdfUrl = URL.createObjectURL(blob);
  const pdfWindow = window.open(pdfUrl, '_blank');

  if (!pdfWindow) {
    const link = document.createElement('a');

    link.href = pdfUrl;
    link.download = response.filename || 'documento.pdf';

    document.body.appendChild(link);
    link.click();
    link.remove();
  }

  setTimeout(() => URL.revokeObjectURL(pdfUrl), 60000);

  return true;
}




