window.AppTable = function (config) {

  const defaults = {
    table: null,
    pageLength: 7,
    responsive: true,
    serverSide: true,
    processing: true,
    searchDelay: 400,
    columns: [],
    columnDefs: [],
    ajax: null,
    extraData: null,

    // Callbacks opcionales
    onEnter: null,
    onEnterEmpty: null,
    onEscape: null,
    onRowSelect: null
  };

  const cfg = { ...defaults, ...config };

  if (!cfg.table) {
    console.error('AppTable: "table" is required');
    return null;
  }

  if ($.fn.DataTable.isDataTable(cfg.table)) {
    $(cfg.table).DataTable().destroy();
  }

  const dt = $(cfg.table).DataTable({
    processing: true,
    serverSide: cfg.serverSide,
    responsive: cfg.responsive,
    pageLength: cfg.pageLength,
    searchDelay: cfg.searchDelay,
    columns: cfg.columns,
    columnDefs: cfg.columnDefs,
    ajax: {
      url: cfg.ajax,
      type: 'POST',
      data: d => {
        if (window.CSRF && CSRF.name && CSRF.hash) {
          d[CSRF.name] = CSRF.hash;
        }
        if (cfg.extraData) cfg.extraData(d);
      },
      dataSrc: json => {
        if (window.CSRF && json.csrfHash) {
          CSRF.hash = json.csrfHash;
        }
        return json.data ?? [];
      }
    },
    // dom: "<'dt-toolbar d-flex justify-content-between align-items-center'Bf>rtip",

    dom: "<'dt-toolbar row'<'col-12 col-lg-6 d-flex justify-content-between'B>" +
      "<'col-12 col-lg-6 d-flex justify-content-end mt-2 mt-lg-0'f>>" +
      "rtip",


    buttons: [
      { extend: 'copy', text: 'Copiar' },
      { extend: 'csv', text: 'CSV' },
      { extend: 'excel', text: 'Excel' },
      { extend: 'pdf', text: 'PDF' },
      { extend: 'print', text: 'Imprimir' },
      { extend: 'colvis', text: 'Columnas' }
    ],
    language: {
      search: "",
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      zeroRecords: "No se encontraron resultados"
    },

    initComplete: function () {
      const table = dt;
      const input = $(cfg.table + '_filter input');

      // 🔥 quitar handlers internos de DataTables
      input.off('.DT');

      input
        .addClass('form-control form-control-lg')
        .attr('placeholder', 'Buscar...')
        .on('keydown', function (e) {
          if (e.key === 'Enter' || e.key === 'Escape') {
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
          }
        })
        .on('keyup', function (e) {
          const value = this.value.trim();

          // ENTER
          if (e.key === 'Enter') {
            if (value.length === 0 && typeof cfg.onEnterEmpty === 'function') {
              cfg.onEnterEmpty(table, this);
            } else if (value.length > 0 && typeof cfg.onEnter === 'function') {
              cfg.onEnter(value, table, this);
            }
            this.value = '';
            return false;
          }

          // ESCAPE
          if (e.key === 'Escape' && typeof cfg.onEscape === 'function') {
            cfg.onEscape(table, this);
            return false;
          }

          // búsqueda normal
          clearTimeout(this._dtTimer);
          this._dtTimer = setTimeout(() => {
            table.search(value).draw();
          }, cfg.searchDelay);
        });

      input.focus();

    }
  });

  return dt;
};


// window.AppTable = function (config) {

//   const defaults = {
//     pageLength: 7,
//     responsive: true,
//     serverSide: true,
//     processing: true,
//     searchDelay: 400,
//     onEnterEmpty: null,
//     onEnter: null
//   };

//   const cfg = { ...defaults, ...config };

//   if ($.fn.DataTable.isDataTable(cfg.table)) {
//     $(cfg.table).DataTable().destroy();
//   }

//   return $(cfg.table).DataTable({
//     processing: true,
//     serverSide: true,
//     responsive: true,
//     pageLength: cfg.pageLength,
//     searchDelay: cfg.searchDelay,
//     columns: cfg.columns,
//     columnDefs: cfg.columnDefs ?? [],

//     dom: "<'dt-toolbar d-flex justify-content-between align-items-center'Bf>rtip",

//     buttons: [
//       { extend: 'copy', text: 'Copiar' },
//       { extend: 'csv', text: 'CSV' },
//       { extend: 'excel', text: 'Excel' },
//       { extend: 'pdf', text: 'PDF' },
//       { extend: 'print', text: 'Imprimir' },
//       { extend: 'colvis', text: 'Columnas' }
//     ],

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

//     language: {
//       search: "",
//       info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
//       zeroRecords: "No se encontraron resultados"
//     },

//     initComplete: function () {

//       const table = $(cfg.table).DataTable();
//       const input = $(cfg.table + '_filter input');

//       // 🔥 quitar handlers internos
//       input.off('.DT');

//       input
//         .addClass('form-control form-control-lg')
//         .attr('placeholder', 'Buscar...')
//         .on('keydown', function (e) {

//           if (e.key === 'Enter') {
//             e.preventDefault();
//             e.stopImmediatePropagation();
//             return false;
//           }
//         })
//         .on('keyup', function (e) {

//           const value = this.value.trim();

//           if (e.key === 'Enter') {
//             e.preventDefault();
//             e.stopImmediatePropagation();

//             if (value.length === 0) {
//               //  input vacío → callback onEnterEmpty
//               if (typeof cfg.onEnterEmpty === 'function') {
//                 cfg.onEnterEmpty(table, this);
//               }
//             } else {
//               //  input con valor → callback onEnter
//               if (typeof cfg.onEnter === 'function') {
//                 cfg.onEnter(value, table, this);
//               }
//             }

//             return false;
//           }

//           // 👉 búsqueda normal
//           clearTimeout(this._dtTimer);
//           this._dtTimer = setTimeout(() => {
//             table.search(value).draw();
//           }, cfg.searchDelay);
//         });

//       input.focus();
//     }
//   });
// };

