async function home_panel_load() {
  try {
    const response = await asyngAjaxSend(
      'invoice/home/panel_load'
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
      id: 'panel_home',
      html: response.html,
      effect: 'fade',
      callback: function () {
        home_init_table();
        card_panel_load();
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


function home_init_table() {
  const table = '#table_invoices';

  if (!$(table).length) {
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
      data: 'customer_name',
      defaultContent: 'SIN CLIENTE',
      render: textRenderer
    },
    {
      data: 'customer_ci',
      defaultContent: '',
      render: textRenderer
    },
    {
      data: 'mount',
      defaultContent: 0,
      className: 'text-end',
      render: function (data, type) {
        const amount = Number(data) || 0;

        if (type !== 'display') {
          return amount;
        }

        return '₲s. ' + amount.toLocaleString('es-PY', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 2
        });
      }
    },
    {
      data: 'user_name',
      defaultContent: 'SIN USUARIO',
      render: textRenderer
    },

    {
      data: 'status_name',
      defaultContent: 'SIN ESTADO',
      render: function (data, type, row) {
        const statusId = Number(row.status_id);

        let label;
        let badgeClass;

        if (statusId === 2) {
          label = 'ANULADO';
          badgeClass = 'bg-danger';
        } else {
          label = 'VÁLIDO';
          badgeClass = 'bg-success';
        }

        if (type !== 'display') {
          return label;
        }

        return '<span class="badge ' + badgeClass + '">' +
          label +
          '</span>';
      }
    },
    {
      data: null,
      orderable: false,
      searchable: false,
      className: 'text-center',
      render: function (data, type, row) {
        if (type !== 'display') {
          return '';
        }

        if (Number(row.status_id) === 2) {
          return '<button type="button" ' +
            'class="btn btn-sm btn-secondary" disabled>' +
            '<i class="fas fa-ban"></i> ' +
            '</button>';
        }

        return '<button type="button" ' +
          'class="btn btn-sm btn-danger btn-invoice-cancel" ' +
          'data-id="' + Number(row.id) + '">' +
          '<i class="fas fa-ban"></i> ' +
          '</button>';
      }
    }
  ];

  window.invoicesTable = AppTable({
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
        const response = await asyngAjaxSend(
          'invoice/home/home_datatable',
          data
        );

        if (
          !response ||
          response.status !== true ||
          !Array.isArray(response.data)
        ) {
          showAlert(
            response?.msg ||
            response?.error ||
            'NO SE PUDIERON CARGAR LAS FACTURAS',
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
        console.error('ERROR AL CARGAR FACTURAS:', error);

        callback({
          draw: Number(data.draw) || 0,
          recordsTotal: 0,
          recordsFiltered: 0,
          data: []
        });

        showAlert(
          'ERROR AL CONSULTAR LAS FACTURAS',
          'danger'
        );
      }
    }
  });
}

$(document).on('click', '.btn-invoice-cancel', async function () {
  const id = Number($(this).attr('data-id'));

  if (!id) {
    showAlert('FACTURA NO VÁLIDA', 'warning');
    return;
  }

  if (!window.confirm(
    '¿ESTÁS SEGURO DE QUE DESEAS ANULAR ESTA FACTURA?'
  )) {
    return;
  }

  try {
    const response = await asyngAjaxSend(
      'invoice/home/cancel_invoice',
      { id: id }
    );

    if (!response || response.status !== true) {
      showAlert(
        response?.msg || 'NO SE PUDO ANULAR LA FACTURA',
        'warning'
      );
      return;
    }

    showAlert(
      response.msg || 'FACTURA ANULADA CORRECTAMENTE',
      'success'
    );

    if (window.invoicesTable) {
      window.invoicesTable.ajax.reload(null, false);
    }
  } catch (error) {
    console.error('ERROR AL ANULAR FACTURA:', error);

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );
  }
});


async function card_panel_load() {

  try {

    const response = await asyngAjaxSend(
      'invoice/home/card_load'
    );

    if (!response || response.status !== true) {

      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDIERON CARGAR LOS INDICADORES',
        'warning'
      );

      return;
    }

    const data = response.data;

    // FORMATEAR MONEDA
    const formatMoney = function (value) {

      return '₲ ' + Number(value || 0).toLocaleString(
        'es-PY',
        {
          minimumFractionDigits: 0,
          maximumFractionDigits: 2
        }
      );

    };

    // FORMATEAR CANTIDAD DE FACTURAS
    const formatQuantity = function (value) {

      const quantity = Number(value || 0);

      return quantity.toLocaleString('es-PY') +
        (
          quantity === 1
            ? ' factura emitida'
            : ' facturas emitidas'
        );

    };

    // ==========================================
    // TARJETAS GENERALES
    // ==========================================

    $('#card_total_sales').text(
      formatMoney(data.general.total)
    );

    $('#card_total_quantity').text(
      formatQuantity(data.general.quantity)
    );

    $('#card_today_total').text(
      formatMoney(data.today.total)
    );

    $('#card_today_quantity').text(
      formatQuantity(data.today.quantity)
    );

    $('#card_year_total').text(
      formatMoney(data.year.total)
    );

    $('#card_year_quantity').text(
      formatQuantity(data.year.quantity)
    );

    $('#card_month_total').text(
      formatMoney(data.month.total)
    );

    $('#card_month_quantity').text(
      formatQuantity(data.month.quantity)
    );

    // ==========================================
    // TARJETAS POR SUCURSAL
    // ==========================================

    const branches = data.branches || [];
    const branchesToday = data.branches_today || [];
    const branchesYear = data.branches_year || [];
    const branchesMonth = data.branches_month || [];

    const branchMap = new Map();

    // SUCURSALES Y TOTAL GENERAL
    branches.forEach(function (branch) {

      const id = Number(branch.sucursal_id);

      branchMap.set(id, {
        id: id,
        name: branch.sucursal_name,

        total: {
          total: Number(branch.total || 0),
          quantity: Number(branch.quantity || 0)
        },

        today: {
          total: 0,
          quantity: 0
        },

        year: {
          total: 0,
          quantity: 0
        },

        month: {
          total: 0,
          quantity: 0
        }
      });

    });

    // TOTAL DE HOY POR SUCURSAL
    branchesToday.forEach(function (branch) {

      const id = Number(branch.sucursal_id);

      if (branchMap.has(id)) {

        branchMap.get(id).today = {
          total: Number(branch.total || 0),
          quantity: Number(branch.quantity || 0)
        };

      }

    });

    // TOTAL DEL AÑO POR SUCURSAL
    branchesYear.forEach(function (branch) {

      const id = Number(branch.sucursal_id);

      if (branchMap.has(id)) {

        branchMap.get(id).year = {
          total: Number(branch.total || 0),
          quantity: Number(branch.quantity || 0)
        };

      }

    });

    // TOTAL DEL MES POR SUCURSAL
    branchesMonth.forEach(function (branch) {

      const id = Number(branch.sucursal_id);

      if (branchMap.has(id)) {

        branchMap.get(id).month = {
          total: Number(branch.total || 0),
          quantity: Number(branch.quantity || 0)
        };

      }

    });

    // ==========================================
    // GENERAR HTML DE LAS SUCURSALES
    // ==========================================

    let html = '';

    branchMap.forEach(function (branch) {

      const branchName = $('<div>')
        .text(branch.name || '')
        .html();

      html += `
                <div class="col-12 mb-4">

                    <h5 class="mb-3">
                        <i class="fa fa-store text-primary mr-2"></i>
                        ${branchName}
                    </h5>

                    <div class="row">

                        <!-- TOTAL GENERAL -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
                            <div class="card border-3 border-top border-top-primary h-100">
                                <div class="card-body">

                                    <h6 class="text-muted">
                                        Total General
                                    </h6>

                                    <h2 class="mb-2">
                                        ${formatMoney(branch.total.total)}
                                    </h2>

                                    <div class="text-success font-weight-bold small">
                                        <i class="fa fa-file-invoice"></i>
                                        ${formatQuantity(branch.total.quantity)}
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- TOTAL DE HOY -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
                            <div class="card border-3 border-top border-top-warning h-100">
                                <div class="card-body">

                                    <h6 class="text-muted">
                                        Total Hoy
                                    </h6>

                                    <h2 class="mb-2">
                                        ${formatMoney(branch.today.total)}
                                    </h2>

                                    <div class="text-success font-weight-bold small">
                                        <i class="fa fa-calendar-day"></i>
                                        ${formatQuantity(branch.today.quantity)}
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- TOTAL DEL AÑO -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
                            <div class="card border-3 border-top border-top-success h-100">
                                <div class="card-body">

                                    <h6 class="text-muted">
                                        Total Este Año
                                    </h6>

                                    <h2 class="mb-2">
                                        ${formatMoney(branch.year.total)}
                                    </h2>

                                    <div class="text-success font-weight-bold small">
                                        <i class="fa fa-calendar"></i>
                                        ${formatQuantity(branch.year.quantity)}
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- TOTAL DEL MES -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
                            <div class="card border-3 border-top border-top-info h-100">
                                <div class="card-body">

                                    <h6 class="text-muted">
                                        Total Este Mes
                                    </h6>

                                    <h2 class="mb-2">
                                        ${formatMoney(branch.month.total)}
                                    </h2>

                                    <div class="text-success font-weight-bold small">
                                        <i class="fa fa-calendar-alt"></i>
                                        ${formatQuantity(branch.month.quantity)}
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            `;

    });

    if (branchMap.size === 0) {

      html = `
                <div class="col-12">
                    <div class="alert alert-light">
                        No hay sucursales registradas.
                    </div>
                </div>
            `;

    }

    $('#invoice_branch_cards').html(html);

  } catch (error) {

    console.error(
      'ERROR AL CARGAR LAS TARJETAS:',
      error
    );

    showAlert(
      'ERROR DE COMUNICACIÓN AL CARGAR LOS INDICADORES',
      'danger'
    );

  }

}



