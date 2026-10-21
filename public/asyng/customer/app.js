
$(document).ready(async function () {
  await panels_load();
});

async function panels_load() {
  await Promise.all([
    home_panel_load(),

  ]);
}

function home_init_table() {
  const table = '#table_customer';

  if (!$(table).length) {
    return;
  }

  // Permisos definidos por PHP en los atributos de la tabla.
  const canView = $(table).attr('data-can-view') === '1';
  const canEdit = $(table).attr('data-can-edit') === '1';
  const canDelete = $(table).attr('data-can-delete') === '1';

  const textRenderer = $.fn.dataTable.render.text();

  // Columnas principales.
  const columns = [
    {
      data: 'id',
      defaultContent: ''
    },
    {
      data: 'ci',
      defaultContent: '',
      render: textRenderer
    },
    {
      data: 'name',
      defaultContent: '',
      render: textRenderer
    },
    {
      data: 'cel',
      defaultContent: '',
      render: textRenderer
    },
    {
      data: 'correo',
      defaultContent: '',
      render: textRenderer
    },
    {
      data: 'credit_status',
      render: function (data, type) {
        const enabled = Number(data) === 1;

        if (type !== 'display') {
          return enabled
            ? 'HABILITADO'
            : 'NO HABILITADO';
        }

        return enabled
          ? '<span class="badge badge-success">HABILITADO</span>'
          : '<span class="badge badge-secondary">NO HABILITADO</span>';
      }
    }
  ];

  // Columna Ver.
  if (canView) {
    columns.push({
      data: null,
      defaultContent: '',
      orderable: false,
      searchable: false,
      render: function (data, type, row) {
        if (type !== 'display') {
          return '';
        }

        return `
                    <button
                        type="button"
                        class="btn btn-outline-info btn-sm customer_view"
                        data-id="${Number(row.id)}"
                        title="Ver cliente">
                        <i class="fas fa-eye"></i>
                    </button>
                `;
      }
    });
  }

  // Columna Editar.
  if (canEdit) {
    columns.push({
      data: null,
      defaultContent: '',
      orderable: false,
      searchable: false,
      render: function (data, type, row) {
        if (type !== 'display') {
          return '';
        }

        return `
                    <button
                        type="button"
                        class="btn btn-outline-primary btn-sm customer_edit"
                        data-id="${Number(row.id)}"
                        title="Editar cliente">
                        <i class="fas fa-edit"></i>
                    </button>
                `;
      }
    });
  }

  // Columna Eliminar.
  if (canDelete) {
    columns.push({
      data: null,
      defaultContent: '',
      orderable: false,
      searchable: false,
      render: function (data, type, row) {
        if (type !== 'display') {
          return '';
        }
        const name = $('<div>').text(row.name ?? '').html();
        return `
                    <button
                        type="button"
                        class="btn btn-outline-danger btn-sm customer_delete"
                        data-id="${Number(row.id)}"
                        data-name="${name}"
                        title="Eliminar cliente">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
      }
    });
  }

  // Inicializar DataTables.
  window.customerTable = AppTable({
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
        const response = await asyngAjaxSend(
          'customer/home/datatable',
          data
        );

        if (!response || response.status !== true) {
          showAlert(
            response?.msg ||
            response?.error ||
            'NO SE PUDIERON CARGAR LOS CLIENTES',
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
          'ERROR AL CARGAR CLIENTES:',
          error
        );

        showAlert(
          'ERROR AL CONSULTAR LOS CLIENTES',
          'danger'
        );

        callback({
          draw: Number(data.draw) || 0,
          recordsTotal: 0,
          recordsFiltered: 0,
          data: []
        });
      }
    }
  });
}
