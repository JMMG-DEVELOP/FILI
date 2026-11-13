// FORMATEO DE INPUT CODE
$(document).on('input', '#code', function () {
  formatCode(this);

});

$(document).on('keydown', '#code', async function (e) {

  if (e.key === 'Enter') {
    e.preventDefault();
    let data = {
      code: $('#code').val()
    };

    const response = await asyngAjaxSend('products/products/form/product/code_validation',
      data
    );
    if (!response.status && response.error === 'code_exists') {
      showAlert(response.message, 'danger');
      return response.error;
    } else if (!response.status && response.error === 'code_empty') {
      showAlert(response.message, 'danger');
      return response.error;
    }
    else if (response.status) {
      showAlert('CODIGO DISPONIBLE', 'success');
      $('#description').focus();
      return response.error;
    }
  }
});
// MANEJO DE MARGENES Y PRECIOS AUTOMATICOS
(function () {

  // ============================================================
  // DESTACAR INPUTS MODIFICADOS
  // ============================================================

  function borderFlash(selector, color = 'red', duration = 3000) {

    const $input = $(selector);

    $input.css({
      border: `2px solid ${color}`,
      transition: 'border 0.2s ease'
    });

    setTimeout(() => {
      $input.css({
        border: '',
        transition: ''
      });
    }, duration);
  }

  // ============================================================
  // CONVERSIONES
  // ============================================================

  function toNumber(value) {
    return Number(String(value || '').replace(/\D/g, '')) || 0;
  }

  function toPercent(value) {

    return Number(
      String(value || '')
        .replace('%', '')
        .replace(',', '.')
        .replace(/[^\d.-]/g, '')
    ) || 0;
  }

  function formatMoney(value) {

    return new Intl.NumberFormat('es-PY', {
      maximumFractionDigits: 0
    }).format(Math.round(value));
  }

  // ============================================================
  // CONFIGURACIÓN
  // ============================================================

  const prices = [
    {
      price: '#price_one',
      margin: '[name="margin_one"]',
      percent: '[name="x1"]'
    },
    {
      price: '#price_two',
      margin: '[name="margin_two"]',
      percent: '[name="x2"]'
    },
    {
      price: '#price_three',
      margin: '[name="margin_three"]',
      percent: '[name="x3"]'
    }
  ];

  // ============================================================
  // CÁLCULO
  // ============================================================

  function calculateProductMargins(changedField = null, item = null, showBorder = true) {

    const cost = toNumber($('#cost').val());

    prices.forEach(current => {

      const priceInput = $(current.price);
      const marginInput = $(current.margin);
      const percentInput = $(current.percent);

      let price = toNumber(priceInput.val());
      let margin = toNumber(marginInput.val());
      let percent = toPercent(percentInput.val());

      // --------------------------------------------------------
      // CAMBIO DE PORCENTAJE
      // --------------------------------------------------------

      if (changedField === 'percent' && item === current) {

        if (cost > 0) {

          margin = Math.round(cost * percent / 100);
          price = cost + margin;

          marginInput.val(formatMoney(margin));
          priceInput.val(formatMoney(price));

          if (showBorder) {
            borderFlash(current.margin);
            borderFlash(current.price);
          }

        } else {

          marginInput.val('');
          priceInput.val('');

          if (showBorder) {
            borderFlash(current.margin);
            borderFlash(current.price);
          }
        }

      }

      // --------------------------------------------------------
      // CAMBIO DE MARGEN
      // --------------------------------------------------------

      else if (changedField === 'margin' && item === current) {

        if (cost > 0) {

          price = cost + margin;
          percent = (margin / cost) * 100;

          priceInput.val(formatMoney(price));
          percentInput.val(percent.toFixed(2) + ' %');

          if (showBorder) {
            borderFlash(current.price);
            borderFlash(current.percent);
          }

        } else {

          priceInput.val('');
          percentInput.val('');

          if (showBorder) {
            borderFlash(current.price);
            borderFlash(current.percent);
          }
        }

      }

      // --------------------------------------------------------
      // CAMBIO DE PRECIO O COSTO
      // --------------------------------------------------------

      else {

        margin = price - cost;

        percent = cost > 0
          ? (margin / cost) * 100
          : 0;

        marginInput.val(
          price > 0 && cost > 0
            ? formatMoney(margin)
            : ''
        );

        percentInput.val(
          price > 0 && cost > 0
            ? percent.toFixed(2) + ' %'
            : ''
        );

        // Al cambiar un precio, solo se resaltan sus campos.
        // Al cambiar el costo, se resaltan los seis campos.
        if (showBorder) {
          borderFlash(current.margin);
          borderFlash(current.percent);
        }
      }

    });
  }

  // ============================================================
  // CAMBIO DE COSTO
  // ============================================================

  $(document)
    .off('input.productMargin', '#cost')
    .on('input.productMargin', '#cost', function () {

      calculateProductMargins(null, null, true);

    });

  // ============================================================
  // CAMBIO DE PRECIOS
  // ============================================================

  $(document)
    .off('input.productMargin', '#price_one, #price_two, #price_three')
    .on('input.productMargin', '#price_one, #price_two, #price_three', function () {

      const item = prices.find(p => p.price === `#${this.id}`);

      calculateProductMargins(null, null, false);

      if (item) {
        borderFlash(item.margin);
        borderFlash(item.percent);
      }

    });

  // ============================================================
  // CAMBIO DE MÁRGENES
  // ============================================================

  $(document)
    .off('input.productMargin', '[name="margin_one"], [name="margin_two"], [name="margin_three"]')
    .on('input.productMargin', '[name="margin_one"], [name="margin_two"], [name="margin_three"]', function () {

      const item = prices.find(p => p.margin === `[name="${this.name}"]`);

      calculateProductMargins('margin', item, true);

    });

  // ============================================================
  // CAMBIO DE PORCENTAJES
  // ============================================================

  $(document)
    .off('input.productMargin', '[name="x1"], [name="x2"], [name="x3"]')
    .on('input.productMargin', '[name="x1"], [name="x2"], [name="x3"]', function () {

      const item = prices.find(p => p.percent === `[name="${this.name}"]`);

      calculateProductMargins('percent', item, true);

    });

  // ============================================================
  // FUNCIÓN PÚBLICA PARA EJECUTAR DESPUÉS DE CARGAR EL FORMULARIO
  // ============================================================

  window.initializeProductMargins = function () {

    calculateProductMargins(null, null, false);

  };

})();
// MANEJO DE CODIGOS POR PESO O UNIDAD

$(document)
  .off('change.productCode', '#sales')
  .on('change.productCode', '#sales', function () {

    const sales = $(this).val();
    const $code = $('#code');

    let code = String($code.val() || '').replace(/\D/g, '');

    if (!code) {
      return;
    }

    // ========================================================
    // DEFINIR PREFIJO SEGÚN TIPO DE VENTA
    // ========================================================

    let prefix = '';

    switch (sales) {

      case '2':
        prefix = '20';
        break;

      case '3':
        prefix = '24';
        break;

      case '4':
        prefix = '31';
        break;

      case '1':
        // Unidad: código normal
        code = code.replace(/^(20|24|31)/, '');
        $code.val(code);
        borderFlash('#code', 'red');
        return;

      default:
        return;
    }

    // ========================================================
    // QUITAR PREFIJOS ANTERIORES
    // ========================================================

    code = code.replace(/^(20|24|31)/, '');

    // ========================================================
    // COMPLETAR A 5 DÍGITOS
    // ========================================================

    code = code.padStart(5, '0').slice(-5);

    // ========================================================
    // FORMAR CÓDIGO FINAL DE 7 DÍGITOS
    // ========================================================

    const newCode = prefix + code;

    $code.val(newCode);

    borderFlash('#code', 'red');

  });

