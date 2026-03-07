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

    invoice_add_product_card(formatted)

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

add_product_cart_search

$(document).on('click', '.product_search_add_cart', function () {

  const code = $(this).data('code')

  product_add_cart(code)

})
