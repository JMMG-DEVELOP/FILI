async function product_search(values) {

  try {
    const response = await asyngAjaxSend(
      'box/controller/product_search',
      values
    );

    if (response.status) {
      asyng_show_view({
        id: 'product_search_panel',
        html: response.html,
        effect: 'fade'
      });
    }


  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor product_search', 'danger');
  } finally {
    $btn.prop('disabled', false);
  }
}
async function product_add_cart(value) {
  try {
    const code = getCode(value);
    const cant = getCant(code);
    const sucursal = $('#select_sucursal').val();

    const response = await asyngAjaxSend(
      'box/invoice/product_add',
      {
        code: code.code,
        sucursal: sucursal
      }
    );

    if (!response.status) {
      showAlert(response.message, 'danger');
      $('#product_cant').val(1);
      $('#search').val('').focus();
      SoundManager.error();
      return;
    }

    const product = getProductData(response.data);
    const percent = getPercent();
    const price = getPrice(product, cant, percent);

    const verify = $('#add_cart_type').is(':checked');

    if (verify) {
      handleVerifySales(product.code);
    } else {
      await addOrUpdateRowCart(product, cant, price, percent);
      formatInputs();
      await update_panel_description(product);
    }

    product_search_table_hide();

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor ADD_CART', 'danger');
  }
}


async function product_search_table_hide() {
  asyng_hide_view({
    id: 'product_search_panel',
    effect: 'fade',
    clear: true
  })

  $("#search").focus()
}