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

    const response = await asyngAjaxSend(
      'box/invoice/product_add',
      { code: code.code }
    );

    if (!response.status) {
      showAlert(response.message, 'danger');
      $('#product_cant').val(1);
      $('#search').val('').focus();
      SoundManager.error();
      return;
    } else {
      const type = Number($('#add_cart_type').val());
      let product = getProductData(response.data);

      switch (type) {
        case 1:
          let percent = getPercent();
          let price = getPrice(product, cant, percent);

          await addOrUpdateRowCart(product, cant, price, percent);
          $('#product_price_input').val('').hide();
          $('#search').val('').focus();
          break;

        case 2:
          handleVerifySales(product.code);
          break;
      }
    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor ADD_CART', 'danger');

  }

}