let CAN_VIEW_COST = false;
let CAN_VIEW_PRODUCT = false;
let CAN_EDIT_PRODUCT = false;
let CAN_DELETE_PRODUCT = false;
let CAN_ADD_PRODUCT = false;
$(document).on('click', '.product_winup', function (e) {
  e.preventDefault();
  home_panel_load();
});
async function home_panel_load() {

  await window_view(
    'products/products/home/home/panel_load',
    'home_panel',
    {},
    function (response) {

      CAN_VIEW_COST = response.data.canViewCost;
      CAN_VIEW_PRODUCT = response.data.canViewProduct;
      CAN_EDIT_PRODUCT = response.data.canEditProduct;
      CAN_DELETE_PRODUCT = response.data.canDeleteProduct;
      CAN_ADD_PRODUCT = response.data.canAddProduct;

      home_datatable();

    }
  );

}

function home_datatable() {

  // ============================================================
  // PERMISOS
  // ============================================================

  const canViewCost = CAN_VIEW_COST;
  const canViewProduct = CAN_VIEW_PRODUCT;
  const canEditProduct = CAN_EDIT_PRODUCT;
  const canDeleteProduct = CAN_DELETE_PRODUCT;
  const canAddProduct = CAN_ADD_PRODUCT;

  // ============================================================
  // FORMATEAR GUARANÍES
  // ============================================================

  const formatGuaranies = (value) => {

    if (
      value === null ||
      value === undefined ||
      value === ''
    ) {
      return '';
    }

    let number = String(value).replace(/\D/g, '');

    number = number.replace(/^0+(?=\d)/, '');

    return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

  };

  // ============================================================
  // FORMATEAR STOCK
  // ============================================================

  const formatStock = (value) => {

    const number = Number(value);

    if (!Number.isFinite(number)) {
      return '';
    }

    return number.toLocaleString('es-ES', {
      minimumFractionDigits: Number.isInteger(number) ? 0 : 2,
      maximumFractionDigits: 2
    });

  };

  // ============================================================
  // CREAR COLUMNA EDITABLE
  // ============================================================

  const editableColumn = (
    field,
    className = 'text-end'
  ) => {

    return {

      data: field,

      className: canEditProduct
        ? `${className} editable-cell`
        : className,

      inlineEditable: canEditProduct

    };

  };

  // ============================================================
  // CREAR COLUMNA DE DINERO
  // ============================================================

  const moneyColumn = (
    field,
    editable = true
  ) => {

    const column = editable
      ? editableColumn(field)
      : {
        data: field,
        className: 'text-end'
      };

    return {

      ...column,

      render: function (data, type, row) {

        // Valor original para ordenar, buscar y editar
        if (type !== 'display') {
          return data;
        }

        return formatGuaranies(data);

      }

    };

  };

  // ============================================================
  // CREAR COLUMNA DE STOCK
  // ============================================================

  const stockColumn = (field) => {

    return {

      ...editableColumn(field),

      render: function (data, type, row) {

        if (type !== 'display') {
          return data;
        }

        return formatStock(data);

      }

    };

  };

  // ============================================================
  // COLUMNAS BASE
  // ============================================================

  let columns = [

    // CÓDIGO
    {
      data: 'code'
    },

    // DESCRIPCIÓN
    editableColumn(
      'description',
      'text-start'
    ),

    // SECCIÓN
    {
      data: 'section'
    }

  ];

  // ============================================================
  // COSTO
  // ============================================================

  if (canViewCost) {

    columns.push(
      moneyColumn('cost')
    );

  }

  // ============================================================
  // PRECIOS Y STOCK
  // ============================================================

  columns = columns.concat([

    // PRECIO 1
    moneyColumn('price_1'),

    // PRECIO 2
    moneyColumn('price_2'),

    // PRECIO 3
    moneyColumn('price_three'),

    // FILI CENTRO - SUCURSAL 1
    {
      ...stockColumn('stock_s1'),
      width: '30px',
      className: 'text-end stock-column'
    },

    // FILI 2 - SUCURSAL 2
    {
      ...stockColumn('stock_s2'),
      width: '30px',
      className: 'text-end stock-column'
    },
    // PRINT
    {
      data: 'print',
      orderable: false,
      searchable: false
    }

  ]);

  // ============================================================
  // VER HISTORIAL
  // ============================================================

  if (canViewProduct) {

    columns.push({

      data: 'view',
      orderable: false,
      searchable: false

    });

  }

  // ============================================================
  // EDITAR
  // ============================================================

  if (canEditProduct) {

    columns.push({

      data: 'edit',
      orderable: false,
      searchable: false

    });

  }

  // ============================================================
  // ELIMINAR
  // ============================================================

  if (canDeleteProduct) {

    columns.push({

      data: 'delete',
      orderable: false,
      searchable: false

    });

  }

  // ============================================================
  // DESTRUIR DATATABLE ANTERIOR
  // ============================================================

  if (
    $.fn.DataTable.isDataTable('#productsTable')
  ) {

    $('#productsTable')
      .DataTable()
      .destroy();

    $('#productsTable tbody').empty();

  }

  // ============================================================
  // INICIALIZAR DATATABLE
  // ============================================================

  window.productsTable = AppTable({

    table: '#productsTable',

    serverSide: true,
    processing: true,
    responsive: true,

    pageLength: 10,
    searchDelay: 600,

    exportButtons: true,

    columns: columns,

    // ==========================================================
    // AJAX
    // ==========================================================

    ajax: async function (data, callback) {

      try {

        const response = await asyngAjaxSend(
          'products/products/home/home/datatable',
          data
        );

        if (
          !response ||
          !Array.isArray(response.data)
        ) {

          showAlert(
            'RESPUESTA INVÁLIDA AL CARGAR LOS PRODUCTOS',
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
          'ERROR AL CARGAR PRODUCTOS:',
          error
        );

        showAlert(
          'ERROR AL CONSULTAR LOS PRODUCTOS',
          'danger'
        );

        callback({

          draw: Number(data.draw) || 0,
          recordsTotal: 0,
          recordsFiltered: 0,
          data: []

        });

      }

    },

    // ==========================================================
    // ENTER EN BUSCADOR
    // ==========================================================

    // onEnter: async function (
    //   value,
    //   table,
    //   input
    // ) {
    //   let val = value.trim();
    //   if (val.length <= 5) {
    //     val = val.padStart(7, '0');
    //     input.value = val;
    //   }

    //   code_validation(val)

    // },
    onEnter: async function (value, table, input) {

      let val = value.trim().toUpperCase();

      // Completar con ceros hasta 7 caracteres
      if (val.length > 0 && val.length < 7) {
        val = val.padStart(7, '0');
      }
      input.value = val;
      code_validation(val);

    },

    onEnterEmpty: function () { },

    // ==========================================================
    // ESCAPE EN BUSCADOR
    // ==========================================================

    onEscape: function (
      table,
      input
    ) {
      table.search('').draw();
      input.value = '';
    }

  });

}

$(document)
  .off('dblclick.productsInline', '#productsTable tbody td')
  .on('dblclick.productsInline', '#productsTable tbody td', function (event) {

    event.preventDefault();
    event.stopPropagation();

    // ============================================================
    // 1. VALIDAR PERMISO
    // ============================================================

    if (!CAN_EDIT_PRODUCT) return;

    const dataTable = $('#productsTable').DataTable();

    const cell = dataTable.cell(this);
    const cellIndex = cell.index();

    if (!cellIndex) return;

    const column = dataTable.settings()[0]
      .aoColumns[cellIndex.column];

    // Solo columnas autorizadas para edición
    if (!column.inlineEditable) return;

    const row = dataTable.row(cellIndex.row);
    const rowData = row.data();

    if (!rowData) return;

    const field = column.data;

    // ============================================================
    // 2. CAMPOS EDITABLES
    // ============================================================

    const allowedFields = [
      'description',
      'cost',
      'price_1',
      'price_2',
      'price_three',
      'stock_s1',
      'stock_s2'
    ];

    if (!allowedFields.includes(field)) return;

    const td = $(this);

    // Evitar múltiples editores
    if (td.find('.inline-edit-input').length) return;

    // ============================================================
    // 3. IDENTIFICAR TIPO DE CAMPO
    // ============================================================

    const isDescription = field === 'description';

    const isGuaranies = [
      'cost',
      'price_1',
      'price_2',
      'price_three'
    ].includes(field);

    // ============================================================
    // 4. VALOR ORIGINAL
    // ============================================================

    const originalValue = String(rowData[field] ?? '');

    // Quitar formato de Guaraníes para comparar y guardar.
    // Ejemplo: 23.500 => 23500

    let numericValue;

    if (isGuaranies) {

      numericValue = Number(
        originalValue.replace(/\D/g, '')
      );

    } else if (!isDescription) {

      numericValue = Number(
        originalValue.replace(',', '.')
      );

    }

    if (
      !isDescription &&
      !Number.isFinite(numericValue)
    ) {
      return;
    }

    // ============================================================
    // 5. GUARDAR HTML ORIGINAL
    // ============================================================

    const originalHtml = td.html();

    // ============================================================
    // 6. CREAR INPUT
    // ============================================================

    const inputClass = [
      'form-control',
      'form-control-sm',
      'inline-edit-input'
    ];

    if (isGuaranies) {
      inputClass.push('guaranies');
    }

    const input = $('<input>', {
      type: 'text',
      class: inputClass.join(' '),
      autocomplete: 'off',
      value: isDescription
        ? originalValue
        : numericValue
    });

    let finished = false;
    let saving = false;

    // Insertar input una sola vez
    td.empty().append(input);

    // ============================================================
    // 7. MOSTRAR EDITOR
    // ============================================================

    td.empty().append(input);

    // Descripción en mayúsculas, conservando el cursor.
    if (isDescription) {

      input.on('input', function () {

        const element = this;
        const start = element.selectionStart;
        const end = element.selectionEnd;

        const upperValue = element.value.toUpperCase();

        if (element.value !== upperValue) {
          element.value = upperValue;
          element.setSelectionRange(start, end);
        }

      });

    }

    // Enfocar sin seleccionar el contenido.
    input[0].focus();
    // ============================================================
    // 8. RESTAURAR CELDA
    // ============================================================

    function restoreCell() {

      if (finished) return;

      finished = true;

      td.html(originalHtml);
    }

    // ============================================================
    // 9. CANCELAR EDICIÓN
    // ============================================================

    function cancelEdit() {

      if (finished || saving) return;

      restoreCell();
    }

    // ============================================================
    // 10. GUARDAR CAMBIOS
    // ============================================================

    async function saveCell() {

      if (finished || saving) return;

      let newValue = String(input.val() ?? '').trim();

      // ----------------------------------------------------------
      // DESCRIPCIÓN
      // ----------------------------------------------------------

      if (isDescription) {

        if (newValue === '') {

          showAlert(
            'LA DESCRIPCIÓN NO PUEDE ESTAR VACÍA',
            'warning'
          );

          restoreCell();
          return;
        }

        if (newValue === originalValue) {
          restoreCell();
          return;
        }

      }

      // ----------------------------------------------------------
      // CAMPOS NUMÉRICOS
      // ----------------------------------------------------------

      if (!isDescription) {

        if (isGuaranies) {

          // Eliminar separadores de miles.
          // 23.500 => 23500
          // 1.500.000 => 1500000

          newValue = newValue.replace(/\D/g, '');

        } else {

          // Stock: conservar decimales.
          newValue = newValue.replace(',', '.');

        }

        const number = Number(newValue);

        if (
          newValue === '' ||
          !Number.isFinite(number) ||
          number < 0
        ) {

          showAlert(
            'INGRESE UN VALOR NUMÉRICO VÁLIDO',
            'warning'
          );

          restoreCell();
          return;
        }

        // Comprobar si realmente cambió el valor
        if (number === numericValue) {
          restoreCell();
          return;
        }

        newValue = number;
      }

      // ========================================================
      // 11. INICIAR AJAX
      // ========================================================

      saving = true;

      input.prop('disabled', true);

      try {

        const data = {
          product_id: rowData.id,
          field: field,
          value: newValue
        };

        const response = await asyngAjaxSend(
          'products/products/home/home/updateInlineField',
          data
        );

        // ======================================================
        // 12. VALIDAR RESPUESTA
        // ======================================================

        if (!response || response.status !== true) {

          saving = false;

          restoreCell();

          showAlert(
            response?.error || 'NO SE PUDO ACTUALIZAR EL VALOR',
            'error'
          );

          return;
        }

        // ======================================================
        // 13. ACTUALIZAR DATATABLE
        // ======================================================

        const savedValue = response.value !== undefined
          ? response.value
          : newValue;

        rowData[field] = savedValue;

        // Actualizar datos de la fila
        row.data(rowData);

        finished = true;

        // Redibujar conservando la página
        dataTable.draw(false);

        showAlert(
          'VALOR ACTUALIZADO CORRECTAMENTE',
          'success'
        );

      } catch (error) {

        saving = false;

        restoreCell();

        console.error(
          'ERROR AL EDITAR PRODUCTO:',
          error
        );

        showAlert(
          'ERROR AL ACTUALIZAR EL VALOR',
          'error'
        );
      }
    }

    // ============================================================
    // 14. ENTER = GUARDAR / ESCAPE = CANCELAR
    // ============================================================

    input.on('keydown', function (event) {

      if (event.key === 'Enter') {

        event.preventDefault();
        event.stopPropagation();

        saveCell();
      }

      if (event.key === 'Escape') {

        event.preventDefault();
        event.stopPropagation();

        cancelEdit();
      }
    });

    // ============================================================
    // 15. BLUR = GUARDAR
    // ============================================================

    input.on('blur', function () {

      saveCell();
    });

  });



