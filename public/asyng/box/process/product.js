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

async function product_add_cart(code) {

  try {

    const response = await asyngAjaxSend(
      'box/invoice/product_add',
      { code: code }
    );

    if (!response.status) {

      showAlert(response.message, 'danger');
      SoundManager.error();
      resetInputs();
      return;

    }

    const type = Number($('#add_card_type').val());

    switch (type) {

      case 1:
        handleDirectSale(response);
        break;

      case 2:
        handleVerifySales(code);
        resetInputs();
        break;

    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor', 'danger');

  }

}