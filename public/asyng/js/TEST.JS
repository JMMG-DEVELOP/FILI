function initProductsTable() {
  const p = window.PRODUCT_PERMISSIONS;

  // Formateadores
  const formatMoney = new Intl.NumberFormat('es-ES', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  });

  const formatStock = (value) => {
    if (Number.isInteger(value)) {
      return new Intl.NumberFormat('es-ES').format(value);
    }
    return new Intl.NumberFormat('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
  };

  // Columnas base
  let columns = [
    { data: 'code' },
    { data: 'description' },
    { data: 'section' }
  ];

  // Columnas sensibles según permiso
  if (p.cost) columns.push({ data: 'cost', className: 'text-end' });

  columns.push(
    { data: 'price_1', className: 'text-end' },
    { data: 'price_2', className: 'text-end' },
    { data: 'price_3', className: 'text-end' },
    { data: 'stock_s1', className: 'text-end' },
    { data: 'stock_s2', className: 'text-end' }
  );

  if (p.edit) columns.push({ data: 'edit', orderable: false, searchable: false });
  if (p.delete) columns.push({ data: 'delete', orderable: false, searchable: false });

  // Render dinámico
  columns = columns.map(col => {
    if (!col.data) return col;

    if (/cost|price/i.test(col.data)) {
      return {
        ...col,
        className: 'text-end',
        render: data => {
          const value = Number(data);
          if (isNaN(value)) return data;
          return formatMoney.format(value);
        }
      };
    }

    if (/stock/i.test(col.data)) {
      return {
        ...col,
        className: 'text-end',
        render: data => formatStock(Number(data))
      };
    }

    return col;
  });

  // Inicializar DataTable
  AppTable({
    table: '#productsTable',
    ajax: BASE_URL + 'products/products/datatable',
    columns: columns,
    searchDelay: 600,

    onEnter: (value, table, input) => {
      let val = value.trim();
      if (val.length <= 5) val = val.padStart(7, '0');
      input.value = val;
      // Aquí puedes abrir un form o acción
    },

    onEnterEmpty: () => alert('vacio'),

    onEscape: (table, input) => {
      table.search('').draw();
      input.value = '';
    },
  });
}
