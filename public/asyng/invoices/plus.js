// FORMATEAR MONTOS EN GUARANÍES
function invoiceFormatMoney(value) {
  return '₲ ' + Number(value || 0).toLocaleString('es-PY', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2
  });
}


// ESCAPAR TEXTO ANTES DE INSERTARLO EN HTML
function invoiceEscapeHtml(value) {
  return String(value ?? '').replace(/[&<>"']/g, function (char) {
    const entities = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };

    return entities[char];
  });
}


// AGREGAR VENTA A LA FACTURA
async function invoice_add_sale(id) {

  id = Number(id);

  if (!id || id <= 0) {
    showAlert('ID DE VENTA NO VÁLIDO', 'warning');
    return;
  }

  // EVITAR AGREGAR LA MISMA VENTA DOS VECES
  if (
    $(`#invoice_sales_selected [data-sale-id="${id}"]`).length > 0
  ) {
    showAlert('ESTA VENTA YA FUE AGREGADA', 'warning');
    return;
  }

  try {

    const response = await asyngAjaxSend(
      'invoice/create/sales_add_invoice',
      { id: id }
    );

    if (!response || response.status !== true) {
      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDO AGREGAR LA VENTA',
        'warning'
      );

      return;
    }

    const sale = response.data;

    if (!sale || !Array.isArray(sale.details)) {
      showAlert(
        'LA RESPUESTA DEL SERVIDOR NO ES VÁLIDA',
        'warning'
      );

      return;
    }

    const saleId = Number(sale.id);
    const total = Number(sale.total_price || 0);
    const details = sale.details;

    const accordionId = `invoice_sale_${saleId}`;
    const headingId = `heading_sale_${saleId}`;
    const collapseId = `collapse_sale_${saleId}`;

    // CONSTRUIR LAS FILAS DE DETALLE
    const rows = details.map(function (detail) {
      return `
                <tr>
                    <td>${invoiceEscapeHtml(detail.product)}</td>
                    <td class="text-end">
                        ${invoiceEscapeHtml(detail.cant)}
                    </td>
                    <td>${invoiceEscapeHtml(detail.descripcion || '')}</td>
                    <td class="text-end">
                        ${invoiceFormatMoney(detail.unit_price)}
                    </td>
                    <td class="text-end">
                        ${invoiceFormatMoney(detail.total_price)}
                    </td>
                </tr>
            `;
    }).join('');

    // CONSTRUIR ACORDEÓN
    const accordion = `
            <div
                class="card mb-2 invoice-sale-item"
                data-sale-id="${saleId}"
                data-sale-total="${total}"
                data-sale-items="${details.length}"
            >

                <div
                    class="card-header"
                    id="${headingId}"
                >
                    <div class="d-flex justify-content-between align-items-center">

                        <button
                            type="button"
                            class="btn btn-link collapsed text-left"
                            data-toggle="collapse"
                            data-target="#${collapseId}"
                            aria-expanded="false"
                            aria-controls="${collapseId}"
                        >
                            Venta #${saleId}
                            -
                            ${invoiceFormatMoney(total)}
                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger invoice-sale-remove"
                            data-id="${saleId}"
                            title="Quitar venta"
                        >
                            <i class="fas fa-times"></i>
                        </button>

                    </div>
                </div>

                <div
                    id="${collapseId}"
                    class="collapse"
                    aria-labelledby="${headingId}"
                    data-parent="#${accordionId}"
                >
                    <div class="card-body">

                        <div class="table-responsive w-100">

                            <table class="table table-sm table-bordered table-striped w-100">

                                <thead>
                                    <tr>
                                        <th>CÓDIGO</th>
                                        <th>CANT.</th>
                                        <th>DESCRIPCIÓN</th>
                                        <th>PRECIO</th>
                                        <th>TOTAL</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    ${rows}
                                </tbody>

                            </table>

                        </div>

                    </div>
                </div>

            </div>
        `;

    // AGREGAR AL CONTENEDOR
    $('#invoice_sales_selected').append(accordion);

    // ACTUALIZAR TOTALES
    invoice_update_totals();

    showAlert('VENTA AGREGADA CORRECTAMENTE', 'success');

  } catch (error) {

    console.error('ERROR AL AGREGAR VENTA:', error);

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );
  }
}


// RECALCULAR TOTAL Y CANTIDAD DE ÍTEMS
function invoice_update_totals() {

  let totalInvoice = 0;
  let itemsInvoice = 0;

  $('#invoice_sales_selected .invoice-sale-item').each(function () {

    totalInvoice += Number($(this).attr('data-sale-total') || 0);
    itemsInvoice += Number($(this).attr('data-sale-items') || 0);

  });

  $('#total_invoice').val(
    totalInvoice.toLocaleString('es-PY', {
      minimumFractionDigits: 0,
      maximumFractionDigits: 2
    })
  );

  $('#items_invoice').val(itemsInvoice);
}


// EVENTO DEL BOTÓN AGREGAR
$(document).on('click', '.btn-sale-add', function (e) {

  e.preventDefault();

  const id = $(this).data('id');

  invoice_add_sale(id);
});


// QUITAR UNA VENTA AGREGADA
$(document).on('click', '.invoice-sale-remove', function (e) {

  e.preventDefault();
  e.stopPropagation();

  const id = Number($(this).data('id'));

  $(`#invoice_sales_selected .invoice-sale-item[data-sale-id="${id}"]`)
    .remove();

  invoice_update_totals();

  showAlert('VENTA QUITADA DE LA FACTURA', 'success');
});