
/**
 * ==========================================================
 * CONTROL DE EFECTIVO - CIERRE DE CAJA
 * ==========================================================
 */


/**
 * ==========================================================
 * FORMATEAR GUARANÍES
 * ==========================================================
 *
 * Ejemplo:
 *
 * 100000
 * ↓
 * ₲ 100.000
 *
 */
function formatGuarani(value) {

  value = Number(value) || 0;

  return '₲ ' + value.toLocaleString('es-PY');

}


/**
 * ==========================================================
 * CALCULAR TOTAL DE UNA DENOMINACIÓN
 * ==========================================================
 *
 * Obtiene:
 *
 * cantidad × valor de la denominación
 *
 * Ejemplo:
 *
 * Cantidad: 5
 * Valor: 100000
 *
 * Total: 500000
 *
 */
function calculateCashRow($input) {

  const quantity = Number($input.val()) || 0;

  const value = Number($input.data('value')) || 0;

  const total = quantity * value;

  return total;

}


/**
 * ==========================================================
 * CALCULAR TODO EL EFECTIVO
 * ==========================================================
 *
 * Recorre todos los inputs:
 *
 * .cash-quantity
 *
 * Calcula el total de cada denominación
 * y posteriormente el total general.
 *
 */
function calculateCashTotal() {

  let grandTotal = 0;


  $('.cash-quantity').each(function () {

    const $input = $(this);

    const total = calculateCashRow($input);


    /**
     * Actualizar total de la fila
     */
    $input
      .closest('tr')
      .find('.cash-total')
      .text(
        formatGuarani(total)
      );


    /**
     * Sumar al total general
     */
    grandTotal += total;

  });


  /**
   * Actualizar total general
   */
  $('#cash_grand_total')
    .text(
      formatGuarani(grandTotal)
    );


  /**
   * Retornar el total
   *
   * Esto será útil cuando
   * conectemos el botón con AJAX.
   */
  return grandTotal;

}


/**
 * ==========================================================
 * VALIDAR CANTIDAD
 * ==========================================================
 *
 * Evita:
 *
 * - números negativos
 * - valores inválidos
 * - cantidades decimales
 *
 */
function validateCashQuantity($input) {

  let value = Number($input.val());


  /**
   * Si el valor no es válido
   */
  if (isNaN(value) || value < 0) {

    value = 0;

  }


  /**
   * Las cantidades de billetes/monedas
   * deben ser números enteros.
   */
  value = Math.floor(value);


  /**
   * Actualizar input
   */
  $input.val(value);


  return value;

}


/**
 * ==========================================================
 * CAMBIO DE CANTIDAD
 * ==========================================================
 *
 * Se ejecuta cuando el usuario escribe
 * una cantidad.
 *
 */
$(document).on(
  'input',
  '.cash-quantity',
  function () {

    const $input = $(this);


    /**
     * Validar cantidad
     */
    validateCashQuantity($input);


    /**
     * Recalcular efectivo
     */
    calculateCashTotal();

  }
);


/**
 * ==========================================================
 * BOTÓN REGISTRAR CIERRE
 * ==========================================================
 */
$(document).on(
  'click',
  '#btn_cash_close',
  function (e) {

    e.preventDefault();


    /**
     * Calcular nuevamente antes
     * de enviar.
     */
    const total = calculateCashTotal();


    console.log(
      'Total efectivo:',
      total
    );


    /**
     * --------------------------------------------------
     * DATOS DEL CIERRE
     * --------------------------------------------------
     *
     * Aquí obtenemos todas las cantidades.
     *
     */
    const cashData = {

      cant_100000: Number(
        $('#cant_100000').val()
      ) || 0,

      cant_50000: Number(
        $('#cant_50000').val()
      ) || 0,

      cant_20000: Number(
        $('#cant_20000').val()
      ) || 0,

      cant_10000: Number(
        $('#cant_10000').val()
      ) || 0,

      cant_5000: Number(
        $('#cant_5000').val()
      ) || 0,

      cant_2000: Number(
        $('#cant_2000').val()
      ) || 0,

      cant_1000: Number(
        $('#cant_1000').val()
      ) || 0,

      cant_500: Number(
        $('#cant_500').val()
      ) || 0,

      cant_100: Number(
        $('#cant_100').val()
      ) || 0,

      cant_50: Number(
        $('#cant_50').val()
      ) || 0,

      total: total

    };


    /**
     * Ver datos antes de enviar.
     */
    console.log(
      'Datos del cierre:',
      cashData
    );


    /**
     * --------------------------------------------------
     * AQUÍ CONECTAREMOS EL AJAX
     * --------------------------------------------------
     *
     * Ejemplo futuro:
     *
     * asyngAjaxSend(
     *     'box/close/save',
     *     cashData,
     *     true
     * );
     *
     */


  }
);


/**
 * ==========================================================
 * INICIALIZAR
 * ==========================================================
 */
$(document).ready(function () {

  /**
   * Calcular valores iniciales.
   *
   * Todos los inputs comienzan
   * con cantidad 0.
   */
  calculateCashTotal();

});
function getMoneyValue(selector) {

  const value = $(selector).val() || '';

  return Number(
    value
      .replace(/[^\d,-]/g, '')
      .replace(/\./g, '')
      .replace(',', '.')
  ) || 0;
}
function getMoneyTextValue(selector) {

  const value = $(selector).text() || '';

  return Number(
    value
      .replace(/[^\d,-]/g, '')
      .replace(/\./g, '')
      .replace(',', '.')
  ) || 0;
}


async function box_close_save() {
  try {
    const cash_total = getMoneyTextValue(
      '#cash_grand_total'
    );

    const qr_total = getMoneyValue(
      '#qr_grand_total'
    );

    const transferencia_total = getMoneyValue(
      '#transferencia_grand_total'
    );

    const tarjeta_total = getMoneyValue(
      '#tarjeta_grand_total'
    );


    const data = {
      cash_total: cash_total,
      qr_total: qr_total,
      transferencia_total: transferencia_total,
      tarjeta_total: tarjeta_total
    };

    const response = await asyngAjaxSend('box/close/box_close_save', data);

    if (response.status) {
      showAlert(response.msg, 'success');
      const resume = {
        box: response.box,
        closing: response.closing
      }
      const close = await asyngAjaxSend('box/close/box_close_resume', resume);
      if (close.status) {
        asyng_show_view({
          id: 'display_close',
          html: close.html,
          effect: 'fade',
        });
      }


    } else {
      showAlert(response.msg, 'warning');
    }

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor box_close_save', 'danger');
  }

}

async function open_box() {
  try {

    const cash_total = getMoneyTextValue(
      '#cash_grand_total'
    );


    /*
     * =================================================
     * VALIDAR MONTO
     * =================================================
     */
    if (cash_total === '' || cash_total === null || isNaN(cash_total)) {

      showAlert(
        'Ingrese el monto de apertura de caja.',
        'warning'
      );

      return;
    }


    /*
     * =================================================
     * DATOS
     * =================================================
     */
    const data = {
      cash_total: cash_total
    };


    /*
     * =================================================
     * ENVIAR
     * =================================================
     */
    const response = await asyngAjaxSend(
      'box/controller/open_box',
      data
    );


    /*
     * =================================================
     * DEBUG
     * =================================================
     */
    console.log('Respuesta open_box:', response);


    /*
     * =================================================
     * VERIFICAR RESPUESTA
     * =================================================
     */
    if (!response) {

      showAlert(
        'El servidor no devolvió una respuesta válida.',
        'danger'
      );

      return;
    }


    /*
     * =================================================
     * ERROR
     * =================================================
     */
    if (response.status !== true) {

      showAlert(
        response.message || 'No se pudo abrir la caja.',
        'warning'
      );

      return;
    }


    /*
     * =================================================
     * APERTURA CORRECTA
     * =================================================
     */
    if (response.status === true) {

      if (!response.redirect) {

        showAlert(
          'La caja fue abierta, pero no se recibió la dirección de destino.',
          'warning'
        );

        return;
      }


      window.location.href = response.redirect;

      return;
    }

  } catch (err) {

    console.error(
      'Error open_box:',
      err
    );

    showAlert(
      'Error de comunicación con el servidor open_box.',
      'danger'
    );
  }
}

