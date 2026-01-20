

function initProductsTable() {

  const canViewCost = CAN_VIEW_COST;
  const canEditProduct = CAN_EDIT_PRODUCT;
  const canDeleteProduct = CAN_DELETE_PRODUCT;


  // Formateadores
  const formatMoney = new Intl.NumberFormat('es-ES', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  });

  const formatStock = (value) => {
    if (Number.isInteger(value)) {
      return new Intl.NumberFormat('es-ES').format(value);
    } else {
      return new Intl.NumberFormat('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
    }
  };

  // Columnas base
  let columns = [
    { data: 'code' },
    { data: 'description' },
    { data: 'section' }
  ];

  // Agregar costo si corresponde
  if (canViewCost) columns.push({ data: 'cost' });

  // Agregar precios, stock y acciones
  columns = columns.concat([
    { data: 'price_1' },
    { data: 'price_2' },
    { data: 'price_3' },
    { data: 'stock_s1' },
    { data: 'stock_s2' },
    // { data: 'edit', orderable: false, searchable: false },
    // { data: 'delete', orderable: false, searchable: false }
  ]);

  if (canEditProduct) columns.push({ data: 'edit' });

  if (canDeleteProduct) columns.push({ data: 'delete' });
  // Aplicar render dinámico según tipo de columna
  columns = columns.map(col => {
    if (!col.data) return col;

    if (/cost|price/i.test(col.data)) {
      return {
        ...col,
        className: 'text-end',
        render: data => {
          const value = Number(data);
          if (isNaN(value)) return data;
          return value.toLocaleString('es-ES', {
            useGrouping: true,
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
          });
        }
      };
    }

    if (/stock/i.test(col.data)) {
      return { ...col, className: 'text-end', render: data => formatStock(Number(data)) };
    }

    return col;
  });

  // Inicializar DataTable
  AppTable({
    table: '#productsTable',
    ajax: BASE_URL + 'products/products/datatable',
    columns: columns,

    onEnter: function (value, table, input) {
      let val = value.trim();
      if (val.length <= 5) {
        val = val.padStart(7, '0');
        input.value = val;
      }

      // FUNCION AL PRESIONAR
    },

    onEnterEmpty: () => {
      alert('vacio');
    },

    onEscape: (table, input) => {
      table.search('').draw();
      input.value = '';
    },
    searchDelay: 600,

  });
}


function initBrandTable() {
  const canEditbrand = CAN_EDIT_BRAND;
  const canDeletebrand = CAN_DELETE_BRAND;
  let columns = [
    {
      data: 'number',
      orderable: false,
      searchable: false,
      className: 'text-center'
    },
    {

      data: 'name'
    },

  ];

  if (canEditbrand) {
    columns.push({
      data: 'edit',
    });
  }
  if (canDeletebrand) {
    columns.push({
      data: 'delete'
    });
  }
  AppTable({
    table: '#brandsTable',
    ajax: BASE_URL + 'products/brands/datatable',
    columns: columns,
    searchDelay: 600,

    onEscape: (table, input) => {
      table.search('').draw();
      input.value = '';
    },
  });

}

function initSectionTable() {
  const canEdit = CAN_EDIT_SECTION;
  const canDelete = CAN_DELETE_SECTION;
  let columns = [
    {
      data: 'number',
      orderable: false,
      searchable: false,
      className: 'text-center'
    },
    {

      data: 'name'
    },

  ];

  if (canEdit) {
    columns.push({
      data: 'edit',
    });
  }
  if (canDelete) {
    columns.push({
      data: 'delete'
    });
  }
  AppTable({
    table: '#sectionTable',
    ajax: BASE_URL + 'products/section/datatable',
    columns: columns,

    onEscape: (table, input) => {
      table.search('').draw();
      input.value = '';
    },
    searchDelay: 600,
  });

}

$(document).ready(function () {
  $('#search').hide();
  initProductsTable();
  initBrandTable();
  initSectionTable();

});



