async function product_search(values) {

  try {
    const response = await asyngAjaxSend(
      'box/process/search',
      values
    );

    if (response.status) {
      asyng_show_view({
        id: 'search_panel',
        html: response.html,
        effect: 'fade'
      });
    }


  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor', 'danger');
  } finally {
    $btn.prop('disabled', false);
  }

}

async function invoice_add_product_card(code) {
  alert(code);
}