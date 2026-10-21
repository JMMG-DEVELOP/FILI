// Evento delegado para el botón Ver.
$(document).on(
  'click',
  '.sales_view_details',
  async function (e) {
    e.preventDefault();

    const id = $(this).data('id');

    if (!id) {
      showAlert(
        'NO SE RECIBIÓ EL ID DE LA VENTA',
        'warning'
      );

      return;
    }

    // Conectar aquí con la función que carga los detalles.
    await sales_details_panel_load(id);
  }
);

async function sales_details_panel_load(id) {
  try {
    const response = await asyngAjaxSend(
      'customer/details/panel_load',
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

    });
  } catch (error) {
    console.error(error);

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );
  }
}

async function anoter_panel_load(id) {
  try {
    const response = await asyngAjaxSend(
      'customer/details/anoter_panel_load',
      { id: id }
    );

    if (!response || response.status !== true) {
      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDO CARGAR EL HISTORIAL DE ANOTACIONES',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_anoter',
      html: response.html,
      effect: 'fade',
      callback: function () {
        customer_anoter_init_table();
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
function customer_anoter_init_table() {
  const table = '#table_anoter';

  if (!$(table).length) {
    return;
  }

  const customerId = Number($(table).attr('data-customer-id'));

  if (!customerId) {
    showAlert('NO SE RECIBIÓ EL ID DEL CLIENTE', 'warning');
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

  window.customerAnoterTable = AppTable({
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
          'customer/details/anoter_datatable',
          data
        );

        if (!response || response.status !== true) {
          showAlert(
            response?.msg ||
            response?.error ||
            'NO SE PUDIERON CARGAR LAS ANOTACIONES',
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
        console.error('ERROR AL CARGAR ANOTACIONES:', error);

        callback({
          draw: Number(data.draw) || 0,
          recordsTotal: 0,
          recordsFiltered: 0,
          data: []
        });

        showAlert(
          'ERROR AL CONSULTAR LAS ANOTACIONES',
          'danger'
        );
      }
    }
  });
}
$(document).on('click', '#details_close', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await customer_sales_panel_load(id);
});