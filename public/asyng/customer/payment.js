
async function payment_panel_load(customer) {
  try {
    const response = await asyngAjaxSend(
      'customer/payment/panel_load',
      {
        customer: customer
      }
    );

    if (!response || response.status !== true) {
      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDO CARGAR EL PANEL DE PAGOS',
        'warning'
      );

      return;
    }

    asyng_show_view({
      id: 'panel_payment',
      html: response.html,
      effect: 'fade',
      callback: function () {
        customer_payment_init_table();
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


function customer_payment_init_table() {
  const table = '#table_payment';

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
      data: 'date',
      defaultContent: '',
      render: textRenderer
    },
    {
      data: 'time',
      defaultContent: '',
      render: textRenderer
    },
    {
      data: 'amount',
      defaultContent: 0,
      className: 'text-right',
      render: function (data, type) {
        const amount = Number(data) || 0;

        if (type !== 'display') {
          return amount;
        }

        return '₲ ' + amount.toLocaleString('es-PY', {
          maximumFractionDigits: 0
        });
      }
    },
    {
      data: 'detail',
      defaultContent: '',
      render: textRenderer
    },
    {
      data: 'user_name',
      defaultContent: '',
      render: textRenderer
    }
  ];

  window.customerPaymentTable = AppTable({
    table: table,
    serverSide: true,
    processing: true,
    responsive: true,
    pageLength: 10,
    searchDelay: 400,
    exportButtons: true,
    order: [[0, 'desc']],
    columns: columns,

    ajax: async function (data, callback) {
      try {
        data.customer = customerId;

        const response = await asyngAjaxSend(
          'customer/payment/payment_datatable',
          data
        );

        if (!response || response.status !== true) {
          showAlert(
            response?.msg ||
            response?.error ||
            'NO SE PUDIERON CARGAR LOS PAGOS',
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
          'ERROR AL CARGAR PAGOS:',
          error
        );

        callback({
          draw: Number(data.draw) || 0,
          recordsTotal: 0,
          recordsFiltered: 0,
          data: []
        });

        showAlert(
          'ERROR AL CONSULTAR LOS PAGOS',
          'danger'
        );
      }
    }
  });
}
