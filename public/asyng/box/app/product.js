$(document).on('keydown', '#search', async function (e) {

  let value = $(this).val().trim()

  let data = { value: value }

  /* SHIFT → buscador */
  if (e.key === 'Shift' && !e.repeat) {

    e.preventDefault()

    if (!value) {

      asyng_hide_view({
        id: 'search_panel',
        effect: 'fade',
        clear: true
      })

      return
    }

    product_search(data)
    $('#search').select()
    return

  }


  if (e.key === 'Enter' && !e.repeat) {

    e.preventDefault()

    /*
     * =====================================================
     * SI EL CAMPO ESTÁ VACÍO
     * =====================================================
     */

    if (!value) {

      const $table = $('#product_search_table')

      /*
       * Verificar que la tabla exista
       */
      if ($table.length > 0) {

        /*
         * Buscar el primer producto de la tabla
         */
        const $firstButton = $table
          .find('tbody .product_search_add_cart')
          .first()

        /*
         * Si existe un resultado
         */
        if ($firstButton.length > 0) {

          /*
           * Obtener código del primer producto
           */
          const code = $firstButton.attr('data-code')

          if (code) {

            const formatted = code.length <= 7
              ? code.padStart(7, '0')
              : code

            /*
             * Colocar el código en el campo
             */
            $(this).val(formatted)

            /*
             * Agregar primer resultado
             */
            product_add_cart(formatted)

            return
          }
        }
      }

      /*
       * No hay código y tampoco resultados
       */
      showAlert('Campo Vacío', 'danger')
      SoundManager.error()

      return
    }


    /*
     * =====================================================
     * SI EL CAMPO TIENE UN CÓDIGO
     * =====================================================
     */

    const formatted = value.length <= 7
      ? value.padStart(7, '0')
      : value

    $(this).val(formatted)

    product_add_cart(formatted)
  }

  // Operacion al Precionar , COMA
  if (e.key === ',') {

    e.preventDefault();

    let val = value.replace(/,$/, '');
    if (val === '' || isNaN(value)) {
      showAlert('COLOCAR VALOR CORRECTO', 'warning');
      $('#search').select();
      return;
    } else {
      $('#other_name').slideDown(200).focus();

    }

  }


});

$(document).on('click', '.product_search_table_hide', function () {
  product_search_table_hide();

});

$(document).on('click', '.product_search_add_cart', function () {

  const code = $(this).data('code')

  product_add_cart(code)
  $('#search').focus()
});

// ¨¨¨¨¨¨¨¨¨¨¨¨¨¨¨¨
// ORDERS
// ...............
$(document).on('click', '#btn_add_orders', async function () {

  const code = $(this).attr('data-code');

  if (!code) {
    showAlert(
      'No se seleccionó ningún producto',
      'warning'
    );
    return;
  }

  const response = await asyngAjaxSend(
    'box/orders/order_add',
    {
      product: code,
      user: $('#user_id').val()
    }
  );

  if (response.status) {

    showAlert(
      'PRODUCTO AÑADIDO A PEDIDO',
      'success'
    );
    $('#btn_add_orders_text').text('Ya en Pedido');
    $('#btn_add_orders_icon')
      .removeClass('fas fa-list-ol')
      .addClass('fas fa-check');

  } else {

    showAlert('PRODUCTO YA ESTA EN PEDIDO', 'danger'
    );

  }

});

