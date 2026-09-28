window.AppTable = function (config) {

  const defaults = {
    table: null,
    pageLength: 8,
    responsive: true,
    serverSide: true,
    processing: true,
    searchDelay: 400,

    columns: [],
    columnDefs: [],

    ajax: null,
    extraData: null,

    exportButtons: true,

    onEnter: null,
    onEnterEmpty: null,
    onEscape: null,
    onRowSelect: null
  };

  const cfg = {
    ...defaults,
    ...config
  };

  if (!cfg.table) {
    console.error('AppTable: "table" is required');
    return null;
  }

  /*
   * Destruir DataTable anterior si existe
   */
  if (
    $.fn.dataTable &&
    $.fn.dataTable.isDataTable(cfg.table)
  ) {
    $(cfg.table).DataTable().destroy();
  }

  let dt;

  /*
   * Configuración base de DataTables
   */
  const dataTableConfig = {

    processing: cfg.processing,

    serverSide: cfg.serverSide,

    responsive: cfg.responsive,

    pageLength: cfg.pageLength,

    searchDelay: cfg.searchDelay,

    dom:
      "<'dt-toolbar row'" +
      "<'col-12 col-lg-6 d-flex justify-content-between'B>" +
      "<'col-12 col-lg-6 d-flex justify-content-end mt-2 mt-lg-0'f>" +
      ">" +
      "rtip",

    buttons: [],

    language: {
      search: "",
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      zeroRecords: "No se encontraron resultados",
      emptyTable: "No hay registros disponibles",
      processing: "Procesando...",
      lengthMenu: "Mostrar _MENU_ registros",
      infoEmpty: "Mostrando 0 a 0 de 0 registros",
      infoFiltered: "(filtrado de _MAX_ registros)",
      paginate: {
        first: "Primero",
        last: "Último",
        next: "Siguiente",
        previous: "Anterior"
      }
    },

    initComplete: function () {

      /*
       * Obtener la instancia directamente desde DataTables
       */
      const table = this.api();

      /*
       * Buscar el input de búsqueda
       */
      const input = $(cfg.table + '_filter input');

      if (!input.length) {
        return;
      }

      /*
       * Eliminar eventos anteriores
       */
      input.off('.DT');

      /*
       * Estilo del buscador
       */
      input
        .addClass('form-control form-control-lg')
        .attr('placeholder', 'Buscar...');

      /*
       * Enter / Escape
       */
      input.on('keydown', function (e) {

        if (
          e.key === 'Enter' ||
          e.key === 'Escape'
        ) {

          e.preventDefault();
          e.stopImmediatePropagation();

          return false;
        }

      });

      /*
       * Búsqueda y eventos personalizados
       */
      input.on('keyup', function (e) {

        const value = this.value.trim();

        /*
         * ENTER
         */
        if (e.key === 'Enter') {

          /*
           * Enter con búsqueda vacía
           */
          if (
            value.length === 0 &&
            typeof cfg.onEnterEmpty === 'function'
          ) {

            cfg.onEnterEmpty(
              table,
              this
            );

          }

          /*
           * Enter con búsqueda
           */
          else if (
            value.length > 0 &&
            typeof cfg.onEnter === 'function'
          ) {

            cfg.onEnter(
              value,
              table,
              this
            );

          }

          /*
           * Limpiar buscador
           */
          this.value = '';

          /*
           * Limpiar búsqueda de DataTables
           */
          table
            .search('')
            .draw();

          return false;
        }

        /*
         * ESCAPE
         */
        if (
          e.key === 'Escape' &&
          typeof cfg.onEscape === 'function'
        ) {

          cfg.onEscape(
            table,
            this
          );

          return false;
        }

        /*
         * Búsqueda automática
         */
        clearTimeout(this._dtTimer);

        this._dtTimer = setTimeout(() => {

          table
            .search(value)
            .draw();

        }, cfg.searchDelay);

      });

      /*
       * Enfocar buscador
       */
      input.focus();
    }
  };

  /*
   * =========================================================
   * COLUMNS
   * =========================================================
   *
   * Solamente agregar "columns" cuando realmente existen.
   *
   * Esto es importante para tablas que ya tienen sus columnas
   * definidas directamente en el HTML.
   */
  if (
    Array.isArray(cfg.columns) &&
    cfg.columns.length > 0
  ) {

    dataTableConfig.columns = cfg.columns;
  }

  /*
   * =========================================================
   * COLUMN DEFS
   * =========================================================
   *
   * Solamente agregar "columnDefs" cuando realmente existen.
   */
  if (
    Array.isArray(cfg.columnDefs) &&
    cfg.columnDefs.length > 0
  ) {

    dataTableConfig.columnDefs = cfg.columnDefs;
  }

  /*
   * =========================================================
   * AJAX
   * =========================================================
   */
  if (cfg.ajax) {

    /*
     * Si ajax es una función
     */
    if (typeof cfg.ajax === 'function') {

      dataTableConfig.ajax = function (
        data,
        callback,
        settings
      ) {

        /*
         * Datos adicionales
         */
        if (
          cfg.extraData &&
          typeof cfg.extraData === 'function'
        ) {

          const extraData = cfg.extraData();

          if (
            extraData &&
            typeof extraData === 'object'
          ) {

            Object.assign(
              data,
              extraData
            );
          }
        }

        /*
         * Ejecutar AJAX personalizado
         */
        cfg.ajax(
          data,
          callback,
          settings
        );
      };

    }

    /*
     * Si ajax es un objeto
     */
    else if (
      typeof cfg.ajax === 'object'
    ) {

      dataTableConfig.ajax = {
        ...cfg.ajax
      };

      /*
       * Agregar datos adicionales
       */
      if (
        cfg.extraData &&
        typeof cfg.extraData === 'function'
      ) {

        const originalData =
          dataTableConfig.ajax.data;

        dataTableConfig.ajax.data =
          function (data) {

            /*
             * Ejecutar data original
             */
            if (
              typeof originalData === 'function'
            ) {

              originalData(data);
            }

            else if (
              originalData &&
              typeof originalData === 'object'
            ) {

              Object.assign(
                data,
                originalData
              );
            }

            /*
             * Agregar datos adicionales
             */
            const extraData =
              cfg.extraData();

            if (
              extraData &&
              typeof extraData === 'object'
            ) {

              Object.assign(
                data,
                extraData
              );
            }
          };
      }
    }

    /*
     * Si ajax es una URL
     */
    else if (
      typeof cfg.ajax === 'string'
    ) {

      dataTableConfig.ajax = {
        url: cfg.ajax,
        type: 'POST'
      };

      /*
       * Datos adicionales
       */
      if (
        cfg.extraData &&
        typeof cfg.extraData === 'function'
      ) {

        dataTableConfig.ajax.data =
          function (data) {

            const extraData =
              cfg.extraData();

            if (
              extraData &&
              typeof extraData === 'object'
            ) {

              Object.assign(
                data,
                extraData
              );
            }
          };
      }
    }
  }

  /*
   * =========================================================
   * BOTONES
   * =========================================================
   */
  if (cfg.exportButtons) {

    const buttons = [];

    /*
     * Excel
     */
    buttons.push({
      extend: 'excelHtml5',
      text: '<i class="fas fa-file-excel"></i>',
      titleAttr: 'Exportar a Excel',
      className: 'btn btn-outline-success'
    });

    /*
     * PDF
     */
    buttons.push({
      extend: 'pdfHtml5',
      text: '<i class="fas fa-file-pdf"></i>',
      titleAttr: 'Exportar a PDF',
      className: 'btn btn-outline-danger',
      orientation: 'landscape',
      pageSize: 'A4'
    });

    /*
     * Imprimir
     */
    buttons.push({
      extend: 'print',
      text: '<i class="fas fa-print"></i>',
      titleAttr: 'Imprimir',
      className: 'btn btn-outline-primary'
    });

    dataTableConfig.buttons = buttons;
  }

  /*
   * =========================================================
   * CREAR DATATABLE
   * =========================================================
   */
  dt = $(cfg.table).DataTable(
    dataTableConfig
  );

  /*
   * =========================================================
   * SELECCIÓN DE FILA
   * =========================================================
   */
  if (
    typeof cfg.onRowSelect === 'function'
  ) {

    $(cfg.table)
      .off(
        'click.AppTable',
        'tbody tr'
      )
      .on(
        'click.AppTable',
        'tbody tr',
        function (e) {

          /*
           * Ignorar si se hizo click en botones,
           * enlaces o controles.
           */
          if (
            $(e.target).closest(
              'button, a, input, select, textarea'
            ).length
          ) {

            return;
          }

          /*
           * Obtener fila
           */
          const row = dt.row(this);

          if (!row.length) {
            return;
          }

          /*
           * Datos de la fila
           */
          const rowData = row.data();

          /*
           * Ejecutar callback
           */
          cfg.onRowSelect(
            rowData,
            row,
            this
          );
        }
      );
  }

  /*
   * =========================================================
   * RETORNAR INSTANCIA
   * =========================================================
   */
  return dt;
};

// window.AppTable = function (config) {

//   const defaults = {
//     table: null,
//     pageLength: 8,
//     responsive: true,
//     serverSide: true,
//     processing: true,
//     searchDelay: 400,
//     columns: [],
//     columnDefs: [],
//     ajax: null,
//     extraData: null,

//     // Callbacks opcionales
//     onEnter: null,
//     onEnterEmpty: null,
//     onEscape: null,
//     onRowSelect: null
//   };

//   const cfg = { ...defaults, ...config };

//   if (!cfg.table) {
//     console.error('AppTable: "table" is required');
//     return null;
//   }

//   if ($.fn.DataTable.isDataTable(cfg.table)) {
//     $(cfg.table).DataTable().destroy();
//   }

//   const dt = $(cfg.table).DataTable({
//     processing: true,
//     serverSide: cfg.serverSide,
//     responsive: cfg.responsive,
//     pageLength: cfg.pageLength,
//     searchDelay: cfg.searchDelay,
//     columns: cfg.columns,
//     columnDefs: cfg.columnDefs,
//     ajax: {
//       url: cfg.ajax,
//       type: 'POST',
//       data: d => {
//         if (window.CSRF && CSRF.name && CSRF.hash) {
//           d[CSRF.name] = CSRF.hash;
//         }
//         if (cfg.extraData) cfg.extraData(d);
//       },
//       dataSrc: json => {
//         if (window.CSRF && json.csrfHash) {
//           CSRF.hash = json.csrfHash;
//         }
//         return json.data ?? [];
//       }
//     },
//     // dom: "<'dt-toolbar d-flex justify-content-between align-items-center'Bf>rtip",

//     dom: "<'dt-toolbar row'<'col-12 col-lg-6 d-flex justify-content-between'B>" +
//       "<'col-12 col-lg-6 d-flex justify-content-end mt-2 mt-lg-0'f>>" +
//       "rtip",


//     buttons: [
//       { extend: 'copy', text: 'Copiar' },
//       { extend: 'csv', text: 'CSV' },
//       { extend: 'excel', text: 'Excel' },
//       { extend: 'pdf', text: 'PDF' },
//       {
//         text: 'JPG',
//         action: function (e, dt, node, config) {

//           // Tomar la tabla dinámica desde cfg
//           let tableEl = document.querySelector(cfg.table);

//           // Si quieres capturar TODO el datatable (toolbar + paginación)
//           // usa esta línea en vez de la de arriba:
//           // let tableEl = document.querySelector(cfg.table).closest('.dataTables_wrapper');

//           html2canvas(tableEl, {
//             scale: 2,
//             useCORS: true
//           }).then(canvas => {

//             let link = document.createElement('a');
//             link.download = 'tabla.jpg';
//             link.href = canvas.toDataURL('image/jpeg', 1.0);
//             link.click();

//           });

//         }
//       },
//       { extend: 'print', text: 'Imprimir' },
//       { extend: 'colvis', text: 'Columnas' },


//     ],

//     language: {
//       search: "",
//       info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
//       zeroRecords: "No se encontraron resultados"
//     },

//     initComplete: function () {
//       const table = dt;
//       const input = $(cfg.table + '_filter input');

//       // 🔥 quitar handlers internos de DataTables
//       input.off('.DT');

//       input
//         .addClass('form-control form-control-lg')
//         .attr('placeholder', 'Buscar...')
//         .on('keydown', function (e) {
//           if (e.key === 'Enter' || e.key === 'Escape') {
//             e.preventDefault();
//             e.stopImmediatePropagation();
//             return false;
//           }
//         })
//         .on('keyup', function (e) {
//           const value = this.value.trim();

//           // ENTER
//           if (e.key === 'Enter') {
//             if (value.length === 0 && typeof cfg.onEnterEmpty === 'function') {
//               cfg.onEnterEmpty(table, this);
//             } else if (value.length > 0 && typeof cfg.onEnter === 'function') {
//               cfg.onEnter(value, table, this);
//             }
//             this.value = '';
//             return false;
//           }

//           // ESCAPE
//           if (e.key === 'Escape' && typeof cfg.onEscape === 'function') {
//             cfg.onEscape(table, this);
//             return false;
//           }

//           // búsqueda normal
//           clearTimeout(this._dtTimer);
//           this._dtTimer = setTimeout(() => {
//             table.search(value).draw();
//           }, cfg.searchDelay);
//         });

//       input.focus();

//     }
//   });

//   return dt;
// };


