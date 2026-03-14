
/*************
 * PANELES LOAD
 */
async function controller_panel_load() {
  try {

    const response = await asyngAjaxSend(
      'box/process/controller_panel_load'
    );

    if (response.status) {

      asyng_show_view({
        id: 'controller_panel',
        html: response.html,
        effect: 'fade',
        callback: function () {
          $('#product_price_input').hide();
          $('#other_name').hide();

        }
      });
    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor controller_panel_load', 'danger');

  }
}

async function payment_panel_load() {
  try {

    const response = await asyngAjaxSend(
      'box/process/payment_panel_load'
    );

    if (response.status) {

      asyng_show_view({
        id: 'payment_panel',
        html: response.html,
        effect: 'fade',
        callback: () => {

          $('#payment_percent').fadeOut(200);
          $('#sales_percent').fadeOut(200);

        }
      });

    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor payment_panel_load', 'danger');

  }

}

async function customer_panel_load(value = '') {
  try {

    const response = await asyngAjaxSend(
      'customer/process/customer_panel_load', { ci: value || '' }
    );

    if (response.status) {

      asyng_show_view({
        id: 'customer_panel',
        html: response.html,
        effect: 'fade'
      });

    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor customer_panel_load()', 'danger');

  }

}

async function panels_load() {

  try {

    await controller_panel_load();
    await payment_panel_load();
    await customer_panel_load();

  } catch (err) {

    showAlert('Error loading panels', err);

  }

}


/*************
 * TOGLES PAYMENT
 */
function toggleSales() {

  const salesType = Number($('#sales').val());

  if (salesType === 2) {

    $('#sales_percent').show();
    $('#payment').closest('.mb-2').hide();
    $('#payment_percent').hide();

  } else {

    $('#sales_percent').hide();
    $('#payment').closest('.mb-2').show();

  }

}

function togglePayment() {

  const paymentType = Number($('#payment').val());

  if ([2, 4].includes(paymentType)) {

    $('#payment_percent').show();

  } else {

    $('#payment_percent').hide();

  }

}