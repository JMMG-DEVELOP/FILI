$(document).on('keydown', '#shopp_code', function (e) {

  if (e.key === 'Enter' && !e.repeat) {
    e.preventDefault();
    const code = $(this).val().trim();
    shopp_win(code)
  }

});
$(document).on('click', '.shopp_select', function (e) {
  e.preventDefault();
  const code = $(this).data('code');
  shopp_win(code)
  asyng_hide_view({
    id: 'shop_search_result',
    effect: 'fade',
    time: 150,
    clear: true
  });

});

function shopp_load_product(product, lastPurchase = null) {

  const $form = $('#shopp_form');

  // ============================================================
  // DESCRIPCIÓN
  // ============================================================

  $form.find('#shopp_code').val(product.code);

  $form.find('#shopp_description').val(product.description);


  // ============================================================
  // COSTO ACTUAL
  // ============================================================

  $form.find('#cost').val(
    formatGuaranies(product.cost)
  );


  // ============================================================
  // PRECIOS
  // ============================================================

  $form.find('#shopp_price_one').val(
    formatGuaranies(product.prices.price_one)
  );

  $form.find('#shopp_price_two').val(
    formatGuaranies(product.prices.price_two)
  );

  $form.find('#shopp_price_three').val(
    formatGuaranies(product.prices.price_three)
  );


  // ============================================================
  // CANTIDADES
  // ============================================================

  $form.find('#shopp_cant_two').val(product.prices.cant_two);

  $form.find('#shopp_cant_three').val(product.prices.cant_three);


  // ============================================================
  // STOCK POR SUCURSAL
  // ============================================================

  Object.entries(product.stock || {}).forEach(([id, stock]) => {

    $form.find('#stock_' + id).val(stock);

  });


  // ============================================================
  // ÚLTIMA COMPRA
  // ============================================================

  shopp_load_last_purchase(lastPurchase);


  // ============================================================
  // RECALCULAR MÁRGENES CON EL COSTO ORIGINAL
  // ============================================================

  window.initializeProductMarginsInit(false);


  // ============================================================
  // MOSTRAR BOTONES
  // ============================================================

  $('.send_shopp').prop('hidden', false);
  $('.cancel_shopp').prop('hidden', false);
  $('.shopp_stock_save').prop('hidden', false);
  $('.calculate_cost').prop('hidden', false);
  $('.print_barcode').prop('hidden', false);
  $('.print_label').prop('hidden', false);
  $('.print_name').prop('hidden', false);

}
function shopp_load_last_purchase(data = null) {

  const $form = $('#shopp_form');

  // ============================================================
  // SI NO EXISTE HISTORIAL, LIMPIAR CAMPOS
  // ============================================================

  if (!data) {

    $form.find('#price_shop_history').val('');
    $form.find('#quantity_shop_history').val('');
    $form.find('#cost_shop_history').val('');
    $form.find('#user_shop_history').val('');
    $form.find('#date_shop_history').val('');

    return;
  }


  // ============================================================
  // PRECIO DE COMPRA
  // ============================================================

  $form.find('#price_shop_history').val(
    Number(data.price).toLocaleString('es-PY', {
      maximumFractionDigits: 0
    })
  );


  // ============================================================
  // CANTIDAD COMPRADA
  // ============================================================

  $form.find('#quantity_shop_history').val(
    data.quantity ?? ''
  );


  // ============================================================
  // PROVEEDOR
  // ============================================================

  $form.find('#cost_shop_history').val(
    data.provider_name ?? ''
  );


  // ============================================================
  // USUARIO
  // ============================================================

  $form.find('#user_shop_history').val(
    data.user_name ?? ''
  );


  // ============================================================
  // FECHA
  // ============================================================

  $form.find('#date_shop_history').val(
    data.date ?? ''
  );

}
async function shopp_win(code) {

  const response = await asyngAjaxSend(
    'products/products/shopp/shopp_win',
    {
      code: code
    }
  );

  if (!response) {
    return;
  }

  const product = response.data?.product;

  if (!product) {

    product_form_open(
      'Nuevo Producto',
      'new',
      'shopp_panel',
      'shopp'
    );

    return;
  }

  shopp_load_product(
    response.data.product,
    response.data.lastPurchase
  );

}

$(document).on('click', '.cancel_shopp', function (e) {
  e.preventDefault();
  shopp_form_reset();
  showAlert('REGISTRO CANCELADO', 'warning');
});
function shopp_form_reset() {

  const form = $('#shopp_form');

  // Guardar los valores de proveedor y sucursal
  const provider = form.find('#provider').val();
  const sucursal = form.find('#scursal').val();

  // Restablecer formulario
  form[0].reset();

  // Limpiar campos de texto, excepto proveedor y sucursal
  form.find('input[type="text"]')
    .not('#provider, #sucursal')
    .val('');

  // Restablecer stocks
  form.find('[name^="stock["]').val('0');

  // Restablecer cantidades
  form.find('#quantity_shop').val('1');
  form.find('#cant_code_print').val('1');
  form.find('#cant_label_print').val('1');

  // Restaurar proveedor y sucursal
  form.find('#provider').val(provider);
  form.find('#sucursal').val(sucursal);

  // Actualizar Select2 si está inicializado
  form.find('#provider, #scursal').trigger('change.select2');

  // Ocultar botones
  form.find('.send_shopp').prop('hidden', true);
  form.find('.cancel_shopp').prop('hidden', true);
  form.find('.shopp_stock_save').prop('hidden', true);
  form.find('.calculate_cost').prop('hidden', true);
  $('.print_barcode').prop('hidden', true);
  $('.print_label').prop('hidden', true);
  $('.print_name').prop('hidden', true);

  $('#shopp_code').focus();
}
$(document)
  .off('click.shoppStock', '.shopp_stock_save')
  .on('click.shoppStock', '.shopp_stock_save', async function () {

    const button = $(this);

    // Sucursal del botón presionado
    const sucursal = button.data('sucursal');

    // Contenedor de esa sucursal
    const container = button.closest('.sucursal-stock');

    // Ajuste ingresado
    const adjustment = container.find('.edit_stock').val();

    // Código del producto
    const code = $('#shopp_code').val();

    if (!code) {
      showAlert('ERROR', 'Primero debes seleccionar un producto');
      return;
    }

    if (adjustment === '' || !Number.isFinite(Number(adjustment)) || Number(adjustment) === 0) {
      showAlert('ERROR', 'Ingresa un ajuste válido');
      return;
    }

    // Evitar doble envío
    button.prop('disabled', true);

    try {

      const response = await asyngAjaxSend(
        'products/products/shopp/update_stock',
        {
          code: code,
          sucursal: sucursal,
          adjustment: adjustment
        }
      );

      if (!response) {
        return;
      }

      // Obtener resultado
      const result = response.data;

      // Actualizar exclusivamente el stock actual
      container.find(`[name="stock[${sucursal}]"]`)
        .val(result.stock);

      // Limpiar ajuste
      container.find('.edit_stock').val('');

      showAlert('STOCK ACTUALIZADO', 'success');

    } finally {

      button.prop('disabled', false);

    }

  });

$(document)
  .off('click.calculateCost', '.calculate_cost')
  .on('click.calculateCost', '.calculate_cost', function (e) {

    e.preventDefault();

    const $form = $('#shopp_form');

    // =========================================
    // 1. OBTENER DATOS
    // =========================================

    let stockActual = 0;

    // Sumar stock de todas las sucursales.
    $form.find('input[name^="stock["]').each(function () {

      let stock = parseFloat(
        String($(this).val()).replace(',', '.')
      ) || 0;

      stockActual += stock;

    });

    // Costo original.
    let costoActual = Number(
      String($form.find('#cost').val()).replace(/\./g, '')
    ) || 0;

    // Precio de compra.
    let precioCompra = Number(
      String($form.find('#price_shop').val()).replace(/\./g, '')
    ) || 0;

    // Cantidad a comprar.
    let cantidadCompra = Number(
      String($form.find('#quantity_shop').val()).replace(',', '.')
    ) || 0;

    // =========================================
    // 2. VALIDACIONES
    // =========================================

    if ($form.find('#cost').val().trim() === '') {
      showAlert('warning', 'El costo actual está vacío');
      $form.find('#cost').focus();
      return;
    }

    if ($form.find('#price_shop').val().trim() === '') {
      showAlert('warning', 'Ingrese el precio de compra');
      $form.find('#price_shop').focus();
      return;
    }

    if ($form.find('#quantity_shop').val().trim() === '') {
      showAlert('warning', 'Ingrese la cantidad de compra');
      $form.find('#quantity_shop').focus();
      return;
    }

    if (precioCompra <= 0) {
      showAlert('warning', 'El precio de compra debe ser mayor a cero');
      $form.find('#price_shop').focus();
      return;
    }

    if (cantidadCompra <= 0) {
      showAlert('warning', 'La cantidad debe ser mayor a cero');
      $form.find('#quantity_shop').focus();
      return;
    }

    // =========================================
    // 3. CALCULAR COSTO PROMEDIO
    // =========================================

    let montoStockActual = stockActual * costoActual;

    let montoNuevaCompra = cantidadCompra * precioCompra;

    let stockTotal = stockActual + cantidadCompra;

    let montoTotal = montoStockActual + montoNuevaCompra;

    if (stockTotal <= 0) {
      showAlert('warning', 'No hay stock suficiente para calcular');
      return;
    }

    let costoPromedio = montoTotal / stockTotal;

    // =========================================
    // 4. GUARDAR COSTO PROMEDIO
    // =========================================

    const costoPromedioFormateado = Math.round(costoPromedio)
      .toLocaleString('es-PY');

    $form.find('[name="cost"]').val(costoPromedioFormateado);

    // =========================================
    // 5. ACTIVAR COSTO PROMEDIO
    // =========================================

    if (typeof window.calculateMarginsWithUpdatedCost === 'function') {

      window.calculateMarginsWithUpdatedCost();

    } else {

      console.error(
        'No existe window.calculateMarginsWithUpdatedCost().'
      );

      showAlert('danger', 'No se pudo activar el costo promedio');
      return;
    }

    // =========================================
    // 6. MOSTRAR RESULTADO
    // =========================================

    console.log('--------------------------------');
    console.log('Stock actual:', stockActual);
    console.log('Costo original:', costoActual);
    console.log('Precio de compra:', precioCompra);
    console.log('Cantidad comprada:', cantidadCompra);
    console.log('Costo promedio:', costoPromedioFormateado);
    console.log('Costo activo:', window.getActiveProductCost());
    console.log('--------------------------------');

  });




(function () {

  // ============================================================
  // CONFIGURACIÓN
  // ============================================================

  const ORIGINAL_COST = '#shopp_form #cost';
  const UPDATED_COST = '#shopp_form [name="cost"]';

  // Estado inicial: costo original.
  let useUpdatedCost = false;

  const prices = [
    {
      price: '#shopp_price_one',
      margin: '[name="shopp_margin_one"]',
      percent: '[name="shopp_x1"]'
    },
    {
      price: '#shopp_price_two',
      margin: '[name="shopp_margin_two"]',
      percent: '[name="shopp_x2"]'
    },
    {
      price: '#shopp_price_three',
      margin: '[name="shopp_margin_three"]',
      percent: '[name="shopp_x3"]'
    }
  ];

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
  // OBTENER COSTO ACTIVO
  // ============================================================

  function getActiveCost() {

    const selector = useUpdatedCost
      ? UPDATED_COST
      : ORIGINAL_COST;

    const value = $(selector).val();

    console.log(
      '[PRODUCT MARGIN] Costo activo:',
      useUpdatedCost ? 'COSTO PROMEDIO (name="cost")' : 'COSTO ORIGINAL (#cost)',
      '| Valor:',
      value
    );

    return toNumber(value);
  }

  // ============================================================
  // DESTACAR CAMPOS
  // ============================================================

  function borderFlash(selector, color = 'red', duration = 3000) {

    const $input = $('#shopp_form').find(selector);

    if (!$input.length) return;

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
  // CALCULAR MÁRGENES Y PORCENTAJES
  // ============================================================

  function calculateProductMargins(
    changedField = null,
    item = null,
    showBorder = true
  ) {

    const cost = getActiveCost();

    const items = item ? [item] : prices;

    items.forEach(current => {

      const $price = $('#shopp_form').find(current.price);
      const $margin = $('#shopp_form').find(current.margin);
      const $percent = $('#shopp_form').find(current.percent);

      if (!$price.length) return;

      let price = toNumber($price.val());
      let margin = toNumber($margin.val());
      let percent = toPercent($percent.val());

      // --------------------------------------------------------
      // CAMBIO DE PORCENTAJE
      // --------------------------------------------------------

      if (changedField === 'percent') {

        if (cost > 0) {

          margin = Math.round(cost * percent / 100);
          price = cost + margin;

          $margin.val(formatMoney(margin));
          $price.val(formatMoney(price));

          if (showBorder) {
            borderFlash(current.margin);
            borderFlash(current.price);
          }

        } else {

          $margin.val('');
          $price.val('');
        }

        return;
      }

      // --------------------------------------------------------
      // CAMBIO DE MARGEN
      // --------------------------------------------------------

      if (changedField === 'margin') {

        if (cost > 0) {

          price = cost + margin;
          percent = (margin / cost) * 100;

          $price.val(formatMoney(price));
          $percent.val(percent.toFixed(2) + ' %');

          if (showBorder) {
            borderFlash(current.price);
            borderFlash(current.percent);
          }

        } else {

          $price.val('');
          $percent.val('');
        }

        return;
      }

      // --------------------------------------------------------
      // CAMBIO DE PRECIO O CAMBIO DE COSTO
      // --------------------------------------------------------

      margin = price - cost;

      percent = cost > 0
        ? (margin / cost) * 100
        : 0;

      $margin.val(
        price > 0 && cost > 0
          ? formatMoney(margin)
          : ''
      );

      $percent.val(
        price > 0 && cost > 0
          ? percent.toFixed(2) + ' %'
          : ''
      );

      if (showBorder) {
        borderFlash(current.margin);
        borderFlash(current.percent);
      }

    });
  }

  // ============================================================
  // CAMBIO DEL COSTO ORIGINAL
  // ============================================================

  $(document)
    .off('input.productMargin', ORIGINAL_COST)
    .on('input.productMargin', ORIGINAL_COST, function () {

      if (!useUpdatedCost) {
        calculateProductMargins(null, null, true);
      }

    });

  // ============================================================
  // CAMBIO DE PRECIOS
  // ============================================================

  $(document)
    .off(
      'input.productMargin',
      '#shopp_form #shopp_price_one, #shopp_form #shopp_price_two, #shopp_form #shopp_price_three'
    )
    .on(
      'input.productMargin',
      '#shopp_form #shopp_price_one, #shopp_form #shopp_price_two, #shopp_form #shopp_price_three',
      function () {

        const item = prices.find(p => p.price === `#${this.id}`);

        if (item) {
          calculateProductMargins(null, item, true);
        }

      }
    );

  // ============================================================
  // CAMBIO DE MÁRGENES
  // ============================================================

  $(document)
    .off(
      'input.productMargin',
      '#shopp_form [name="shopp_margin_one"], #shopp_form [name="shopp_margin_two"], #shopp_form [name="shopp_margin_three"]'
    )
    .on(
      'input.productMargin',
      '#shopp_form [name="shopp_margin_one"], #shopp_form [name="shopp_margin_two"], #shopp_form [name="shopp_margin_three"]',
      function () {

        const item = prices.find(p => p.margin === `[name="${this.name}"]`);

        if (item) {
          calculateProductMargins('margin', item, true);
        }

      }
    );

  // ============================================================
  // CAMBIO DE PORCENTAJES
  // ============================================================

  $(document)
    .off(
      'input.productMargin',
      '#shopp_form [name="shopp_x1"], #shopp_form [name="shopp_x2"], #shopp_form [name="shopp_x3"]'
    )
    .on(
      'input.productMargin',
      '#shopp_form [name="shopp_x1"], #shopp_form [name="shopp_x2"], #shopp_form [name="shopp_x3"]',
      function () {

        const item = prices.find(p => p.percent === `[name="${this.name}"]`);

        if (item) {
          calculateProductMargins('percent', item, true);
        }

      }
    );

  // ============================================================
  // INICIALIZAR PRODUCTO: COSTO ORIGINAL
  // ============================================================

  window.initializeProductMarginsInit = function () {

    useUpdatedCost = false;

    console.log('[PRODUCT MARGIN] Reiniciado: costo original');

    calculateProductMargins(null, null, false);

  };

  // ============================================================
  // CAMBIAR A COSTO PROMEDIO
  // ============================================================

  window.calculateMarginsWithUpdatedCost = function () {

    const $updatedCost = $(UPDATED_COST);

    if (!$updatedCost.length) {
      console.error('[PRODUCT MARGIN] No existe el campo name="cost".');
      return;
    }

    useUpdatedCost = true;

    console.log(
      '[PRODUCT MARGIN] Cambiado a costo promedio:',
      $updatedCost.val()
    );

    calculateProductMargins(null, null, true);

  };

  // ============================================================
  // CONSULTAR COSTO ACTIVO
  // ============================================================

  window.getActiveProductCost = function () {
    return getActiveCost();
  };

})();
