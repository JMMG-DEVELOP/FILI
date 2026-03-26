
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

async function expedition_point_load() {
  try {

    const response = await asyngAjaxSend(
      'box/process/expedition_point_load'
    );

    if (response.status) {

      asyng_show_view({
        id: 'expedition_point_panel',
        html: response.html,
        effect: 'fade',
        callback: () => {
          expedition_point_values();
        }
      });

    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor expedition_point_load', 'danger');

  }
}

async function panels_load() {

  try {

    await controller_panel_load();
    await payment_panel_load();
    await customer_panel_load();
    await expedition_point_load()

  } catch (err) {
    showAlert('Error loading panels', err);
  }

}


/*************
 * TOGLES PAYMENT
 */
function toggleSales() {

  const salesType = Number($('#sales').val());

  if (salesType === 2 || salesType === 3) {

    if (salesType === 3) {
      $('#sales_percent').hide();
    } else {
      $('#sales_percent').show();
    }
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

function formatInputs() {
  $('#product_price_input').val('').hide();
  $('#display_escape').hide();
  $('#display_other_pay').hide();
  $('#product_cant').val(1);

  $('#cash_payment').val('');
  $('#change').text('VUELTO');

  $('#search').val('').focus();

}

async function cancelAll() {

  $('#cart_invoice tbody').empty();
  $('#search').focus();
  $('#display_escape').hide();
  $('#display_other_pay').hide();

  await customer_panel_load();
  await payment_panel_load();

  updateCartCount();
  updateGrandTotal();
  saveCart();
  SoundManager.warning();
}