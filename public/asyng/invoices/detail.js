async function sales_detail_panel(id, div, type) {

  if (!id || Number(id) <= 0) {
    showAlert(
      'ID DE VENTA NO VÁLIDO',
      'warning'
    );
    return;
  }

  try {

    const response = await asyngAjaxSend(
      'invoice/create/sales_detail_panel',
      {
        id: Number(id),
        type: type
      }
    );

    if (!response || response.status !== true) {
      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDIERON CARGAR LOS DETALLES',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: div,
      html: response.html,
      effect: 'fade'
    });

  } catch (error) {

    console.error(
      'ERROR AL CARGAR DETALLES:',
      error
    );

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );
  }
}

async function invoice_detail_panel(id, div, type) {

  if (!id || Number(id) <= 0) {
    showAlert(
      'ID DE VENTA NO VÁLIDO',
      'warning'
    );
    return;
  }

  try {

    const response = await asyngAjaxSend(
      'invoice/create/invoice_detail_panel',
      {
        id: Number(id),
        type: type
      }
    );

    if (!response || response.status !== true) {
      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDIERON CARGAR LOS DETALLES',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: div,
      html: response.html,
      effect: 'fade'
    });

  } catch (error) {

    console.error(
      'ERROR AL CARGAR DETALLES:',
      error
    );

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );
  }
}
// ABRIR DETALLE DE LA VENTA
$(document).on('click', '.btn-sale-view', function (e) {

  e.preventDefault();

  const id = $(this).data('id');

  sales_detail_panel(id, 'sales_panel', 'create');
});

$(document).on('click', '.btn-detail-view', function (e) {

  e.preventDefault();

  const id = $(this).data('id');

  invoice_detail_panel(id, 'panel_home', 'home');
});



// CERRAR DETALLE DE LA VENTA
$(document).on('click', '#sales_detail_close', function (e) {

  e.preventDefault();

  sales_panel();
});
$(document).on('click', '#sales_detail_home_close', function (e) {

  e.preventDefault();

  home_panel_load();
});