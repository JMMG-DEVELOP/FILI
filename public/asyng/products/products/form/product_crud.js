
$(document)
  .off('submit.productSave', '#product_form')
  .on('submit.productSave', '#product_form', async function (e) {

    e.preventDefault();

    const $form = $(this);
    const $button = $form.find('[type="submit"]');

    if ($button.prop('disabled')) return;

    // Determinar operación
    const operation = $form.find('#operation_type').val();

    const isEdit = operation === 'edit';

    const url = isEdit
      ? 'products/products/form/product/update'
      : 'products/products/form/product/create';

    try {

      $button.prop('disabled', true);

      const data = asyngFormData('#product_form');

      const response = await asyngAjaxSend(url, data);

      if (response.status) {

        showAlert(response.msg, 'success');

        $('.product_form_close').trigger('click');

        if ($.fn.DataTable.isDataTable('#products_table')) {
          $('#products_table').DataTable().ajax.reload(null, false);
        }
        shopp_panel_load()
      } else {

        showAlert(
          response.msg || 'No se pudo guardar el producto', 'error',
        );

      }

    } catch (error) {

      console.error('ERROR AL GUARDAR PRODUCTO:', error);

      showAlert(
        error.responseJSON?.msg ||
        'Ocurrió un error al guardar el producto', 'error'
      );

    } finally {

      $button.prop('disabled', false);

    }

  });

