async function wait_save() {
  try {

    const customer = asyngFormData('#form_customer');
    const cart = sales_cart_data();

    const data = {
      customer,
      cart
    };

    // Validar si el cliente ya tiene una espera
    const validation = await asyngAjaxSend('box/wait/wait_validation', data);

    let response;

    if (validation.status != false) {

      const update = {
        cart: cart,
        wait: validation.status.id,
        mount: validation.status.mount
      };
      // Ya existe, actualizar
      response = await asyngAjaxSend('box/wait/wait_update', update);

    } else {
      response = await asyngAjaxSend('box/wait/wait_save', data);

    }

    if (response.status) {
      $('#cart_invoice tbody').empty();
      await wait_panel_load();
      await payment_panel_load();
      await customer_panel_load();
      $('#search').focus();
      $('#display_escape').hide();
      $('#display_other_pay').hide();
      procedures_hide();
      updateCartCount();
      updateGrandTotal();
      saveCart();
      showAlert('AÑADIDO EN ESPERA', 'success');
    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor wait_save', 'danger');

  }
}

async function wait_list(wait) {

  try {

    const response = await asyngAjaxSend('box/wait/wait_list', {
      wait: wait
    });

    if (!response.status) {
      showAlert('No se encontraron productos en espera', 'warning');
      return;
    }

    $('#cart_invoice tbody').empty();

    response.values.forEach(item => {

      const product = {
        id: item.id,
        code: item.code,
        description: item.description,
        stock: 0,

        price_one: parseFloat(item.price_one),
        price_two: parseFloat(item.price_two),
        cant_two: parseFloat(item.cant_two),

        cost: parseFloat(item.cost),
        iva: parseFloat(item.iva)
      };

      const cant = parseFloat(item.cant);
      const price = parseFloat(item.price_one);

      createRow(product, cant, price, false);

    });

    updateCartCount();
    updateGrandTotal();
    saveCart();

    const resp = await asyngAjaxSend('box/wait/wait_delete', {
      wait: wait
    });
    if (resp.status) {
      wait_panel_load();
      $('#search').focus();
    }


  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor wait_list', 'danger');

  }
}