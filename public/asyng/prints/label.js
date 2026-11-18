(function () {

  // ============================================================
  // IMPRESIÓN DE ETIQUETA
  // ============================================================

  window.imprimirLabel = async function (
    code,
    description,
    price_1,
    price_2,
    cant_2,
    quantity
  ) {

    try {

      // ========================================================
      // 1. VALIDACIONES
      // ========================================================

      if (!code || !String(code).trim()) {
        throw new Error('EL CÓDIGO DEL PRODUCTO ES OBLIGATORIO.');
      }

      if (!description || !String(description).trim()) {
        throw new Error('LA DESCRIPCIÓN DEL PRODUCTO ES OBLIGATORIA.');
      }

      quantity = Number(quantity);

      if (!Number.isInteger(quantity) || quantity <= 0) {
        throw new Error('LA CANTIDAD DE ETIQUETAS NO ES VÁLIDA.');
      }

      // ========================================================
      // 2. ENVIAR DATOS AL CONTROLADOR
      // ========================================================

      const response = await asyngAjaxSend(
        'prints/label',
        {
          code: code,
          description: description,
          price_1: price_1,
          price_2: price_2,
          cant_2: cant_2,
          quantity: quantity
        },
        true
      );

      if (!response || response.status !== true) {
        throw new Error(
          response?.msg || 'NO SE PUDO PREPARAR LA ETIQUETA.'
        );
      }

      const data = response.data;

      if (!data || !data.printer || !data.commands) {
        throw new Error(
          'LA RESPUESTA DEL SERVIDOR NO CONTIENE LOS DATOS DE IMPRESIÓN.'
        );
      }

      // ========================================================
      // 3. VERIFICAR QZ TRAY
      // ========================================================

      if (typeof qz === 'undefined') {
        throw new Error('QZ TRAY NO ESTÁ DISPONIBLE.');
      }

      if (!qz.websocket.isActive()) {
        await qz.websocket.connect();
      }

      // ========================================================
      // 4. BUSCAR IMPRESORA
      // ========================================================

      const printerName = await qz.printers.find(
        data.printer.printer
      );

      if (!printerName) {
        throw new Error(
          'NO SE ENCONTRÓ LA IMPRESORA: '
          + data.printer.printer
        );
      }

      // ========================================================
      // 5. CONFIGURAR IMPRESORA
      // ========================================================

      const config = qz.configs.create(
        printerName,
        {
          encoding: 'UTF-8',
          jobName: 'Label ' + code
        }
      );

      // ========================================================
      // 6. ENVIAR COMANDOS TSPL
      // ========================================================

      await qz.print(
        config,
        [
          {
            type: 'raw',
            format: 'command',
            flavor: 'base64',
            data: data.commands
          }
        ]
      );

      return {
        status: true,
        msg: 'TRABAJO ENVIADO A LA IMPRESORA.',
        quantity: quantity
      };

    } catch (error) {

      console.error(
        'ERROR IMPRESIÓN LABEL:',
        error
      );

      showAlert(
        error.message || 'ERROR AL IMPRIMIR LA ETIQUETA.',
        'danger'
      );

      return {
        status: false,
        msg: error.message
      };
    }

  };

})();