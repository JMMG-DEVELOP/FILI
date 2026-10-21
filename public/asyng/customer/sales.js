
async function customer_sales_panel_load(id) {
  try {
    const response = await asyngAjaxSend(
      'customer/sales/panel_load',
      { id: id }
    );

    if (!response || response.status !== true) {
      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDO CARGAR EL PANEL DE VENTAS',
        'warning'
      );

      return;
    }

    asyng_show_view({
      id: 'panel_sales',
      html: response.html,
      effect: 'fade',
      callback: async function () {
        await customer_sales_card_load(id);
        await customer_sales_init_table();
      }
    });
  } catch (error) {
    console.error(error);

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );
  }
}


function customer_sales_init_table() {
  const table = '#table_sales';

  if (!$(table).length) {
    return;
  }

  const customerId = Number(
    $(table).attr('data-customer-id')
  );

  if (!customerId) {
    showAlert(
      'NO SE RECIBIÓ EL ID DEL CLIENTE',
      'warning'
    );

    return;
  }

  if ($.fn.DataTable.isDataTable(table)) {
    $(table).DataTable().destroy();
  }

  const textRenderer = $.fn.dataTable.render.text();

  const columns = [
    {
      data: 'id',
      defaultContent: ''
    },
    {
      data: 'number',
      defaultContent: '',
      render: textRenderer
    },
    {
      data: 'type_name',
      defaultContent: '',
      render: textRenderer
    },
    {
      data: 'invoice_type_name',
      defaultContent: '',
      render: textRenderer
    },
    {
      data: 'date_time',
      defaultContent: '',
      render: textRenderer
    },
    {
      data: 'total',
      defaultContent: 0,
      className: 'text-right',
      render: function (data, type) {
        const amount = Number(data) || 0;

        if (type !== 'display') {
          return amount;
        }

        return amount.toLocaleString('es-PY', {
          maximumFractionDigits: 0
        });
      }
    },
    {
      data: 'payment_name',
      defaultContent: 'SIN PAGO',
      render: textRenderer
    },
    {
      data: 'sucursal_name',
      defaultContent: '',
      render: textRenderer
    },
    {
      data: 'margin',
      defaultContent: 0,
      className: 'text-right',
      render: function (data, type) {
        const amount = Number(data) || 0;

        if (type !== 'display') {
          return amount;
        }

        return amount.toLocaleString('es-PY', {
          maximumFractionDigits: 0
        });
      }
    },
    {
      data: 'user_name',
      defaultContent: '',
      render: textRenderer
    },
    {
      data: null,
      defaultContent: '',
      orderable: false,
      searchable: false,
      className: 'text-center',
      render: function (data, type, row) {
        if (type !== 'display') {
          return '';
        }

        return `
                    <button
                        type="button"
                        class="btn btn-outline-info btn-sm sales_view_details"
                        data-id="${Number(row.id)}"
                        title="Ver detalles de la venta">
                        <i class="fas fa-eye"></i>
                        Ver
                    </button>
                `;
      }
    }
  ];

  window.customerSalesTable = AppTable({
    table: table,
    serverSide: true,
    processing: true,
    responsive: true,
    pageLength: 10,
    searchDelay: 400,
    exportButtons: true,
    columns: columns,
    ajax: async function (data, callback) {
      try {
        data.customer = customerId;

        const response = await asyngAjaxSend(
          'customer/sales/sales_datatable',
          data
        );

        if (!response || response.status !== true) {
          showAlert(
            response?.msg ||
            response?.error ||
            'NO SE PUDIERON CARGAR LAS VENTAS',
            'warning'
          );

          callback({
            draw: Number(data.draw) || 0,
            recordsTotal: 0,
            recordsFiltered: 0,
            data: []
          });

          return;
        }

        callback(response);
      } catch (error) {
        console.error(
          'ERROR AL CARGAR VENTAS:',
          error
        );

        callback({
          draw: Number(data.draw) || 0,
          recordsTotal: 0,
          recordsFiltered: 0,
          data: []
        });

        showAlert(
          'ERROR AL CONSULTAR LAS VENTAS',
          'danger'
        );
      }
    }
  });
}


async function customer_sales_card_load(customerId) {

  try {

    const response = await asyngAjaxSend(
      'customer/sales/sales_card',
      {
        customer: customerId
      }
    );

    if (!response || response.status !== true) {

      showAlert(
        response?.msg || 'NO SE PUDIERON CARGAR LOS TOTALES',
        'warning'
      );

      return;
    }

    const data = response.data;

    const formatMoney = value =>
      '₲ ' + (Number(value) || 0).toLocaleString('es-PY', {
        maximumFractionDigits: 0
      });

    $('#card_total_sales').text(formatMoney(data.total_sales));
    $('#card_total_cost').text(formatMoney(data.total_cost));
    $('#card_total_margin').text(formatMoney(data.total_margin));

    $('#card_total_purchases').text(
      Number(data.total_purchases || 0).toLocaleString('es-PY')
    );

    $('#card_total_credit').text(formatMoney(data.total_credit));

    $('#card_total_credit_purchases').text(
      Number(data.total_credit_purchases || 0).toLocaleString('es-PY')
    );

  } catch (error) {

    console.error('ERROR AL CARGAR TARJETAS DE VENTAS:', error);

    showAlert(
      'ERROR DE COMUNICACIÓN AL CARGAR LOS TOTALES',
      'danger'
    );
  }
}


