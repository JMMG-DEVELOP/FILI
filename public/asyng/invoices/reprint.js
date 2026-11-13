/**
 * REIMPRIMIR FACTURA
 * - Compatible con botones cargados mediante AJAX.
 * - Obtiene el PDF desde el controlador.
 * - Imprime mediante QZ Tray.
 * - No modifica la factura ni la secuencia de numeración.
 */

$(document)
  .off('click.invoiceReprint', '.btn-invoice-reprint')
  .on('click.invoiceReprint', '.btn-invoice-reprint', async function (e) {
    e.preventDefault();

    const $button = $(this);

    // Evitar múltiples impresiones por clics repetidos.
    if ($button.data('printing') === true) {
      return;
    }

    const invoiceId = Number($button.data('id'));

    if (!Number.isInteger(invoiceId) || invoiceId <= 0) {
      showAlert('ID DE FACTURA NO VÁLIDO', 'warning');
      return;
    }

    $button.data('printing', true);

    const originalHtml = $button.html();
    const originalDisabled = $button.prop('disabled');

    try {
      $button.prop('disabled', true);
      $button.html(
        '<span class="spinner-border spinner-border-sm" role="status"></span> Imprimiendo...'
      );

      // 1. Solicitar al servidor el PDF de la factura existente.
      const response = await asyngAjaxSend(
        'invoice/home/reprint',
        {
          invoice_id: invoiceId
        },
        true
      );

      // 2. Validar respuesta.
      if (!response || response.status !== true) {
        throw new Error(
          response?.error || 'NO SE PUDO GENERAR EL PDF DE LA FACTURA'
        );
      }

      if (!response.pdf || typeof response.pdf !== 'string') {
        throw new Error('EL SERVIDOR NO DEVOLVIÓ EL PDF DE LA FACTURA');
      }

      if (
        !response.printer ||
        !response.printer.printer_system_name
      ) {
        throw new Error('NO SE ENCONTRÓ LA IMPRESORA CONFIGURADA');
      }

      // 3. Imprimir el PDF mediante QZ Tray.
      await imprimirPDFBase64(
        response.pdf,
        response.printer,
        response.filename ||
        `factura_reimpresion_${invoiceId}.pdf`
      );

      // 4. Confirmación.
      showAlert('FACTURA REIMPRESA CORRECTAMENTE', 'success');

    } catch (error) {
      console.error('ERROR AL REIMPRIMIR FACTURA:', error);

      showAlert(
        error?.message || 'NO SE PUDO REIMPRIMIR LA FACTURA',
        'danger'
      );

    } finally {
      // Restaurar botón incluso si falla la impresión.
      $button.prop('disabled', originalDisabled);
      $button.html(originalHtml);
      $button.removeData('printing');
    }
  });


/**
 * IMPRIMIR PDF BASE64 MEDIANTE QZ TRAY
 *
 * Requiere que QZ Tray esté cargado en la página
 * y que su configuración de certificados/firma esté resuelta
 * según la configuración actual de tu aplicación.
 */
async function imprimirPDFBase64(pdfBase64, printer, filename) {
  if (!pdfBase64 || typeof pdfBase64 !== 'string') {
    throw new Error('EL PDF RECIBIDO NO ES VÁLIDO');
  }

  if (!printer || !printer.printer_system_name) {
    throw new Error('NO SE RECIBIÓ EL NOMBRE DE LA IMPRESORA');
  }

  // Verificar que QZ Tray esté disponible.
  if (typeof qz === 'undefined') {
    throw new Error('QZ TRAY NO ESTÁ CARGADO EN LA PÁGINA');
  }

  try {
    // Conectar si todavía no existe una conexión activa.
    if (!qz.websocket.isActive()) {
      await qz.websocket.connect();
    }

    // Seleccionar la impresora configurada en el servidor.
    const config = qz.configs.create(
      printer.printer_system_name
    );

    // Enviar el PDF a imprimir.
    await qz.print(config, [
      {
        type: 'pixel',
        format: 'pdf',
        flavor: 'base64',
        data: pdfBase64
      }
    ]);

    return true;

  } catch (error) {
    console.error('ERROR DE QZ TRAY:', error);

    throw new Error(
      'NO SE PUDO IMPRIMIR EL PDF. VERIFICA QZ TRAY Y LA IMPRESORA.'
    );
  }
}