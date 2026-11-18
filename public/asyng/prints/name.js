
(function (window, $) {

  'use strict';

  // ============================================================
  // IMPRIMIR NOMBRE
  // ============================================================

  async function imprimirName(name, quantity) {

    try {

      // ----------------------------------------------------
      // 1. VALIDAR DATOS
      // ----------------------------------------------------

      name = String(name || '').trim();

      quantity = parseInt(quantity, 10);

      if (!name) {
        throw new Error(
          'EL NOMBRE DEL PRODUCTO ES OBLIGATORIO'
        );
      }

      if (!Number.isInteger(quantity) || quantity < 1) {
        throw new Error(
          'LA CANTIDAD DEBE SER MAYOR A CERO'
        );
      }

      // ----------------------------------------------------
      // 2. PREPARAR IMPRESIÓN EN SERVIDOR
      // ----------------------------------------------------

      const response = await asyngAjaxSend(
        'prints/name',
        {
          name: name,
          quantity: quantity
        },
        true
      );

      if (!response || response.status !== true) {
        throw new Error(
          response?.msg ||
          'NO SE PUDO PREPARAR EL NOMBRE'
        );
      }

      const printer = response.data;

      if (!printer || !printer.commands) {
        throw new Error(
          'NO SE RECIBIERON LOS COMANDOS TSPL2'
        );
      }

      // ----------------------------------------------------
      // 3. VALIDAR QZ TRAY
      // ----------------------------------------------------

      if (typeof qz === 'undefined') {
        throw new Error(
          'QZ TRAY NO ESTÁ CARGADO'
        );
      }

      // ----------------------------------------------------
      // 4. CONECTAR QZ TRAY
      // ----------------------------------------------------

      if (!qz.websocket.isActive()) {
        await qz.websocket.connect();
      }

      // ----------------------------------------------------
      // 5. VERIFICAR IMPRESORA
      // ----------------------------------------------------

      await qz.printers.find(printer.printer);

      // ----------------------------------------------------
      // 6. CONFIGURAR IMPRESORA
      // ----------------------------------------------------

      const config = qz.configs.create(
        printer.printer,
        {
          encoding: 'UTF-8',
          jobName: 'Name ' + name
        }
      );

      // ----------------------------------------------------
      // 7. ENVIAR TSPL2 RAW
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
        'IMPRESIÓN DE NOMBRE ENVIADA',
        {
          name: printer.name,
          quantity: printer.quantity,
          printer: printer.printer,
          dpi: printer.dpi,
          width: printer.printable,
          warnings: printer.warnings
        }
      );

      return {
        status: true,
        method: 'qz-tspl2-name',
        name: name,
        quantity: quantity,
        warnings: printer.warnings || []
      };

    } catch (error) {

      console.error(
        'ERROR AL IMPRIMIR NOMBRE:',
        error
      );

      showAlert(
        error.message ||
        'NO SE PUDO IMPRIMIR EL NOMBRE',
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

  window.imprimirName = imprimirName;

  // ============================================================
  // EVENTO DEL BOTÓN
  // ============================================================

  $(document).on(
    'click',
    '.print_name',
    async function (e) {

      e.preventDefault();

      const button = $(this);

      if (button.prop('disabled')) {
        return;
      }

      const name = $('#shopp_description').val();

      const quantity = $('#cant_code_print').val();

      const originalHTML = button.html();

      try {

        button
          .prop('disabled', true)
          .html(
            '<i class="fas fa-spinner fa-spin"></i> Imprimiendo...'
          );

        const result = await window.imprimirName(
          name,
          quantity
        );

        if (result.status === true) {

          showAlert(
            'NOMBRE ENVIADO A LA IMPRESORA. CANTIDAD: '
            + quantity,
            'success'
          );

          result.warnings.forEach(function (warning) {
            showAlert(warning, 'warning');
          });

        }

      } finally {

        button
          .prop('disabled', false)
          .html(originalHTML);

      }

    }
  );

})(window, jQuery);
