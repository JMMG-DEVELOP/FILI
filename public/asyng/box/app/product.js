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

    return

  }

  /* ENTER */
  if (e.key === 'Enter' && !e.repeat) {

    e.preventDefault()

    if (!value) {

      showAlert('Campo Vacío', 'warning')
      SoundManager.error()
      return

    }

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

  asyng_hide_view({
    id: 'product_search_panel',
    effect: 'fade',
    clear: true
  })

  $("#search").focus()

});

$(document).on('click', '.product_search_add_cart', function () {

  const code = $(this).data('code')

  product_add_cart(code)

});


