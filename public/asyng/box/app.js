$(document).on('keydown', '#search', async function (e) {

  let value = $(this).val().trim();

  let data = {
    value: value
  };

  /**
   * SHIFT → abrir buscador modal
   */
  if (e.key === 'Shift' && !e.repeat) {

    e.preventDefault();
    if (!value) {

      asyng_hide_view({
        id: 'search_panel',
        effect: 'fade',
        clear: true
      });

      return;
    }

    product_search(data);
    return;
  }

  /**
   * ENTER → buscar / validar
   */
  if (e.key === 'Enter' && !e.repeat) {

    e.preventDefault();

    if (!value) {

      showAlert('Campo Vacío', 'warning');

      asyng_hide_view({
        id: 'search_panel',
        effect: 'fade',
        clear: true
      });

      return;
    }

    const formatted = value.length <= 7
      ? value.padStart(7, '0')
      : value;
    $(this).val(formatted);

    invoice_add_product_card(formatted)

  }

});
