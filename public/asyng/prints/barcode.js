
(function (window, $) {

  'use strict';

  // ============================================================
  // IMPRIMIR CÓDIGO DE BARRAS TSPL2
  // ============================================================

  async function imprimirBarcode(code, quantity) {

    try {

      // ----------------------------------------------------
      // 1. VALIDAR DATOS
      // ----------------------------------------------------

      code = String(code || '').trim();

      quantity = parseInt(quantity, 10);

      if (!code) {
        throw new Error(
          'EL CÓDIGO DEL PRODUCTO ES OBLIGATORIO'
        );
      }

      if (!Number.isInteger(quantity) || quantity < 1) {
        throw new Error(
          'LA CANTIDAD DEBE SER MAYOR A CERO'
        );
      }

      // ----------------------------------------------------
      // 2. OBTENER COMANDOS DEL SERVIDOR
      // ----------------------------------------------------

      const response = await asyngAjaxSend(
        'prints/barcode',
        {
          code: code,
          quantity: quantity
        },
        true
      );

      if (!response || response.status !== true) {

        throw new Error(
          response?.msg ||
          response?.error ||
          'NO SE PUDO PREPARAR EL CÓDIGO DE BARRAS'
        );

      }

      const printer = response.data;

      if (!printer) {
        throw new Error(
          'NO SE RECIBIÓ LA CONFIGURACIÓN DE IMPRESIÓN'
        );
      }

      // ----------------------------------------------------
      // 3. VALIDAR CONFIGURACIÓN
      // ----------------------------------------------------

      if (!printer.printer) {
        throw new Error(
          'NO SE RECIBIÓ EL NOMBRE DE LA IMPRESORA'
        );
      }

      if (!printer.commands) {
        throw new Error(
          'NO SE RECIBIERON LOS COMANDOS TSPL2'
        );
      }

      if (Number(printer.dpi) !== 203) {
        throw new Error(
          'LA RESOLUCIÓN DEBE SER 203 DPI'
        );
      }

      // ----------------------------------------------------
      // 4. VALIDAR QZ TRAY
      // ----------------------------------------------------

      if (typeof qz === 'undefined') {
        throw new Error(
          'QZ TRAY NO ESTÁ CARGADO'
        );
      }

      // ----------------------------------------------------
      // 5. CONECTAR QZ TRAY
      // ----------------------------------------------------

      if (!qz.websocket.isActive()) {
        await qz.websocket.connect();
      }

      // ----------------------------------------------------
      // 6. VERIFICAR IMPRESORA
      // ----------------------------------------------------

      await qz.printers.find(printer.printer);

      // ----------------------------------------------------
      // 7. CONFIGURAR IMPRESORA
      // ----------------------------------------------------

      const config = qz.configs.create(
        printer.printer,
        {
          encoding: 'UTF-8',

          jobName: 'Barcode ' + code
        }
      );

      // ----------------------------------------------------
      // 8. ENVIAR TSPL2 RAW
      // ----------------------------------------------------

      const printData = [
        {
          type: 'raw',
          format: 'command',
          flavor: 'base64',
          data: printer.commands
        }
      ];

      await qz.print(
        config,
        printData
      );

      console.log(
        'IMPRESIÓN TSPL2 ENVIADA',
        {
          code: code,
          quantity: quantity,
          printer: printer.printer,
          dpi: printer.dpi,
          width: printer.printable,
          height: printer.height
        }
      );

      return {
        status: true,
        method: 'qz-tspl2',
        code: code,
        quantity: quantity
      };

    } catch (error) {

      console.error(
        'ERROR AL IMPRIMIR CÓDIGO:',
        error
      );

      showAlert(
        error.message ||
        'NO SE PUDO IMPRIMIR EL CÓDIGO DE BARRAS',
        'danger'
      );

      return {
        status: false,
        error: error.message
      };
    }
  }

  // ============================================================
  // FUNCIÓN GLOBAL REUTILIZABLE
  // ============================================================

  window.imprimirBarcode = imprimirBarcode;

})(window, jQuery);
