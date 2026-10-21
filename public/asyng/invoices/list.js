async function sales_panel() {
  try {
    const response = await asyngAjaxSend(
      'invoice/create/sales_panel'
    );

    if (!response || response.status !== true) {
      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDO CARGAR ',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'sales_panel',
      html: response.html,
      effect: 'fade',
      callback: async function () {
        await initInvoiceSalesTable();
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

function initInvoiceSalesTable() {

  AppTable({
    table: '#table_sales_invoice',

    pageLength: 10,
    serverSide: true,
    processing: true,
    responsive: true,
    searchDelay: 400,

    ajax: async function (data, callback) {

      try {

        const response = await asyngAjaxSend(
          'invoice/create/sales_datatable',
          data
        );

        if (!response || response.status === false) {

          showAlert(
            response?.msg ||
            response?.error ||
            'NO SE PUDIERON CARGAR LAS VENTAS',
            'warning'
          );

          callback({
            draw: data.draw,
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
          draw: data.draw,
          recordsTotal: 0,
          recordsFiltered: 0,
          data: []
        });

        showAlert(
          'ERROR DE COMUNICACIÓN AL CARGAR LAS VENTAS',
          'danger'
        );
      }
    },

    columns: [
      {
        data: 'id'
      },
      {
        data: 'number',
        defaultContent: '-'
      },
      {
        data: 'sucursal_name',
        defaultContent: 'SIN SUCURSAL'
      },
      {
        data: 'total_price',

        render: function (data, type) {

          const amount = Number(data || 0);

          if (type !== 'display') {
            return amount;
          }

          return '₲ ' + amount.toLocaleString(
            'es-PY',
            {
              minimumFractionDigits: 0,
              maximumFractionDigits: 2
            }
          );
        }
      },
      {
        data: null,
        orderable: false,
        searchable: false,

        render: function (data, type, row) {

          if (type !== 'display') {
            return '';
          }

          return `
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-info btn-sale-view"
                            data-id="${Number(row.id)}"
                            title="Ver venta"
                        >
                            <i class="fa fa-eye"></i>
                        </button>
                    `;
        }
      },
      {
        data: null,
        orderable: false,
        searchable: false,

        render: function (data, type, row) {

          if (type !== 'display') {
            return '';
          }

          return `
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-success btn-sale-add"
                            data-id="${Number(row.id)}"
                            title="Agregar venta a la boleta"
                        >
                            <i class="fa fa-plus"></i>
                            Agregar
                        </button>
                    `;
        }
      }
    ],

    columnDefs: [
      {
        targets: [0, 4, 5],
        className: 'text-center'
      },
      {
        targets: 3,
        className: 'text-right'
      }
    ],

    exportButtons: false
  });
}
