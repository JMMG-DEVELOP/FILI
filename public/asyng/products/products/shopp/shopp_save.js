// Evitar que ENTER envíe el formulario accidentalmente
$(document)
  .off('keydown.shoppEnter', '#shopp_form input')
  .on('keydown.shoppEnter', '#shopp_form input', function (e) {

    if (e.key === 'Enter') {
      e.preventDefault();
      e.stopPropagation();
      return false;
    }

  });

$(document).on('click', '.send_shopp', async function (e) {

  e.preventDefault();

  const button = $(this);

  try {

    if (!validateForm('#shopp_form')) {
      return;
    }

    button.prop('disabled', true);

    const formData = asyngFormData('#shopp_form');

    const response = await asyngAjaxSend(
      'products/products/shopp/save',
      formData
    );

    if (response && response.status) {

      showAlert(
        response.msg || 'COMPRA REGISTRADA CORRECTAMENTE',
        'success'
      );

      // Datos devueltos por el controlador
      const data = response.data || {};

      // Actualizar stock de la sucursal correspondiente
      if (
        data.sucursal !== undefined &&
        data.stock !== undefined
      ) {

        $('#shopp_form')
          .find('[name="stock[' + data.sucursal + ']"]')
          .val(data.stock);

      }
      shopp_form_reset()
      showAlert('REGISTRO EXITOSO', 'success');

    }

  } catch (error) {

    console.error(error);

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );

  } finally {

    button.prop('disabled', false);

  }

});