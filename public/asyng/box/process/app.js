
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
async function print_panel_load() {
  try {

    const response = await asyngAjaxSend(
      'box/process/print_panel_load'
    );

    if (response.status) {

      asyng_show_view({
        id: 'print_panel',
        html: response.html,
        effect: 'fade',

      });

    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor payment_panel_load', 'danger');

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

async function box_movement_panel_load() {
  try {

    const response = await asyngAjaxSend(
      'box/process/box_movement_panel_load'
    );

    if (response.status) {

      asyng_show_view({
        id: 'box_movement_panel',
        html: response.html,
        effect: 'fade',
        callback: () => {
          asyngMoneyMask();
          $('#mount').focus()
        }
      });

    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor box_movement_panel_load', 'danger');

  }
}

async function wait_panel_load() {
  try {

    const response = await asyngAjaxSend(
      'box/process/wait_panel_load'
    );

    if (response.status) {

      asyng_show_view({
        id: 'wait_panel',
        html: response.html,
        effect: 'fade',

      });

    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor wait_panel_load', 'danger');

  }
}

async function invoice_panel_load() {
  try {

    const response = await asyngAjaxSend(
      'box/process/invoice_product_panel'
    );

    if (response.status) {

      asyng_show_view({
        id: 'invoice_panel',
        html: response.html,
        effect: 'fade',
        callback: () => {
          updateGrandTotal();
          $('search').focus();
        }
      });

    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor invoice_panel', 'danger');

  }
}

async function panels_load() {

  try {

    await controller_panel_load();
    await print_panel_load();
    await payment_panel_load();
    await customer_panel_load();
    await history_sales_panel();
    await history_movements_panel();
    await wait_panel_load();
    await invoice_panel_load();
    await expedition_point_load();
    $('#btn_add_orders').hide();

  } catch (err) {
    showAlert('Error loading panels', err);
  }

}
let currentPage = 1;

async function history_sales_panel(type = null, page = 1) {

  let pageCash = window.historyPageCash || 1;
  let pageCredit = window.historyPageCredit || 1;
  let pageOther = window.historyPageOther || 1;

  /*
   * Guardar la pestaña que estaba activa
   * antes de reemplazar el HTML.
   */
  let activeTab = $('#history_sales_panel .nav-tabs .nav-link.active')
    .attr('href');

  // Si no existe todavía, Efectivo por defecto
  if (!activeTab) {
    activeTab = '#cash';
  }

  // Cambiar página de EFECTIVO
  if (type === 'cash') {
    pageCash = Math.max(1, Number(page));
    activeTab = '#cash';
  }

  // Cambiar página de CRÉDITO
  if (type === 'credit') {
    pageCredit = Math.max(1, Number(page));
    activeTab = '#credit';
  }

  // Cambiar página de OTROS
  if (type === 'other') {
    pageOther = Math.max(1, Number(page));
    activeTab = '#other';
  }

  // Guardar las páginas actuales
  window.historyPageCash = pageCash;
  window.historyPageCredit = pageCredit;
  window.historyPageOther = pageOther;

  const data = {
    page_cash: pageCash,
    page_credit: pageCredit,
    page_other: pageOther
  };

  try {

    const response = await asyngAjaxSend(
      'box/process/history_sales_panel_load',
      data
    );

    if (!response || !response.status) {

      showAlert(
        response?.error || 'ERROR AL CARGAR EL HISTORIAL',
        'error'
      );

      return;
    }

    /*
     * Reemplazar el contenido.
     */
    $('#history_sales_panel').html(response.html);

    /*
     * Restaurar la pestaña que estaba activa.
     */
    $('#history_sales_panel .nav-tabs a[href="' + activeTab + '"]').tab('show');

  } catch (error) {

    console.error('history_sales_panel:', error);

    showAlert(
      'ERROR DE COMUNICACIÓN AL CARGAR EL HISTORIAL',
      'error'
    );
  }
}


async function history_sales_detail_panel(id) {
  try {

    const data = {
      id: id
    }
    const response = await asyngAjaxSend(
      'box/process/history_sales_details_panel_load', data
    );

    if (response.status) {

      asyng_show_view({
        id: 'history_sales_panel',
        html: response.html,
        effect: 'fade'
      });

    }

  } catch (err) {

    console.error(err);

    showAlert(
      'Error al cargar movimientos de caja',
      'danger'
    );

  }

}
async function history_movements_panel() {

  try {

    const response = await asyngAjaxSend(
      'box/process/history_movements_panel_load'
    );

    if (response.status) {

      asyng_show_view({
        id: 'history_movements_panel',
        html: response.html,
        effect: 'fade'
      });

    }

  } catch (err) {

    console.error(err);

    showAlert(
      'Error al cargar movimientos de caja',
      'danger'
    );

  }

}

async function invoice_digist_panel_load() {
  try {

    const response = await asyngAjaxSend(
      'box/process/invoice_digits_panel'
    );

    if (response.status) {

      asyng_show_view({
        id: 'invoice_panel',
        html: response.html,
        effect: 'fade',
        callback: () => {
          $('#cash_grand_total').text(returnGrandTotal());
          $('#payment_device_operation_number').focus()
        }

      });
    }

  } catch (err) {

    console.error(err);
    showAlert('Error de comunicación con el servidor invoice_digits_panel_load', 'danger');

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


async function clear() {
  $('#cart_invoice tbody').empty();
  $('#search').focus();
  $('#receipt_type').val('1').trigger('change');
  $('#print_type').val('1').trigger('change');
  $('#sales').val('1').trigger('change');
  $('#payment').val('1').trigger('change');
  $('#ruc_ci').text('1');
  $('#customer_name').text('CLIENTE OCASIONAL');
  $('#all_price_two').prop('checked', false);

  await expedition_point_load();
  await invoice_panel_load();
  await history_sales_panel();
  await multi_payment_hide();
  clearCart();
  saveCart()

}
async function searh_focus() {
  await invoice_panel_load();

}