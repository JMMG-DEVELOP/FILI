// / Abrir panel view
$(document).on('click', '.customer_view', async function (e) {
  e.preventDefault();
  const id = $(this).data('id');
  await Promise.all([
    customer_view_panel_load(id),

  ]);

});
async function customer_view_panel_load(id) {
  const data = {
    id: id
  }
  try {
    const response = await asyngAjaxSend(
      'customer/view/view_panel_load', data
    );

    if (!response || response.status !== true) {
      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDO CARGAR EL PANEL DE CLIENTES',
        'warning'
      );

      return;
    }

    asyng_show_view({
      id: 'panel_home',
      html: response.html,
      effect: 'fade',
      callback: async function () {
        await Promise.all([
          home_view_load(id),
          customer_sales_panel_load(id),
          payment_panel_load(id),
          anoter_panel_load(id)

        ]);
      }
    });

  } catch (error) {

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );
  }
}

async function home_view_load(id) {

  try {
    const data = {
      id: id
    }
    const response = await asyngAjaxSend(
      'customer/view/home_view_load', data
    );

    if (!response || response.status !== true) {
      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDO CARGAR EL PANEL DE CLIENTES',
        'warning'
      );

      return;
    }

    asyng_show_view({
      id: 'panel_view_home',
      html: response.html,
      effect: 'fade',

    });

  } catch (error) {

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );
  }
}

// Abrir formulario para pagar la cuenta directamente

$(document).on('click', '#credit_payment_form_open', async function (e) {
  e.preventDefault();
  await credit_payment_form_open();
});
async function credit_payment_form_open() {

  try {
    $('#credit_payment_form_open').hide();
    $('#credit_add_form_open').hide();
    const data = {
      id: $('#customer_id').val(),
      customer: $('#form_customer_credit [name="customer"]').val()
    };
    const response = await asyngAjaxSend(
      'customer/view/credit_payment_form_open', data
    );

    if (!response || response.status !== true) {
      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDO CARGAR EL PANEL DE CLIENTES',
        'warning'
      );

      return;
    }

    asyng_show_view({
      id: 'panel_view_form',
      html: response.html,
      effect: 'fade',
      callback: function () {
        $('#amount').focus();
      }
    });

  } catch (error) {

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );
  }
}
$(document).on('keydown', '#amount', function (e) {

  if (e.key === 'Enter') {
    e.preventDefault();

    const amount = $(this).val().trim();

    if (amount !== '') {
      $('input[name="detail"]').trigger('focus');
    } else {
      $(this).trigger('focus');
    }
  }

});
// Guardar Pago 
$(document).on('click', '#credit_payment_form_save', async function (e) {
  e.preventDefault();
  await credit_payment_form_save();
});

async function credit_payment_form_save() {

  try {

    if (!validateForm('#form_customer_save')) {
      return;
    }

    const values = asyngFormData('#form_customer_save');

    const data = {
      values: values
    };

    const response = await asyngAjaxSend(
      'customer/view/credit_payment_form_save',
      data
    );

    if (!response || response.status !== true) {

      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDO REALIZAR EL PAGO',
        'warning'
      );

      return;
    }

    // Obtener el ID del cliente devuelto por el controlador.
    const customerId = response.data?.customer;

    // Obtener el vuelto, si existe.
    const change = Number(response.data?.change || 0);

    // Cerrar el formulario de pago.
    $('#panel_view_form').html('');

    // Restaurar los botones.
    $('#credit_payment_form_open').show();
    $('#credit_add_form_open').show();

    // Recargar la ventana del cliente correcto.
    if (customerId) {
      await customer_view_panel_load(customerId);
    }

    // Mostrar el resultado.
    showAlert(
      change > 0
        ? `PAGO REGISTRADO CORRECTAMENTE. VUELTO: ${change.toLocaleString('es-PY')}`
        : (response.msg || 'PAGO REGISTRADO CORRECTAMENTE'),
      'success'
    );

  } catch (err) {

    console.error(err);

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );

  }
}

// Abrir formulario para AÑADIR CREDITO la cuenta directamente

$(document).on('click', '#credit_add_form_open', async function (e) {
  e.preventDefault();
  await credit_add_form_open();
});

async function credit_add_form_open() {
  try {
    $('#credit_payment_form_open').hide();
    $('#credit_add_form_open').hide();

    const customerId = $('#id_cliente').val();

    if (
      !customerId ||
      customerId === '-' ||
      !Number.isInteger(Number(customerId)) ||
      Number(customerId) <= 0
    ) {
      showAlert(
        'NO SE PUDO IDENTIFICAR AL CLIENTE',
        'warning'
      );
      return;
    }

    const response = await asyngAjaxSend(
      'customer/view/credit_add_form_open',
      {
        customer: customerId
      }
    );

    if (!response || response.status !== true) {
      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDO CARGAR EL FORMULARIO',
        'warning'
      );
      return;
    }

    asyng_show_view({
      id: 'panel_view_form',
      html: response.html,
      effect: 'fade',
      callback: function () {
        $('#amount').trigger('focus');
      }
    });

  } catch (error) {
    console.error('ERROR AL ABRIR EL FORMULARIO:', error);

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );
  } finally {
    $('#credit_payment_form_open').show();
    $('#credit_add_form_open').show();
  }
}
// Guardar Suma de Credito 

$(document).on('click', '#credit_add_form_save', async function (e) {
  e.preventDefault();
  await credit_add_form_save();
});

async function credit_add_form_save() {
  try {
    if (!validateForm('#form_customer_save')) {
      return;
    }

    const values = asyngFormData('#form_customer_save');

    const response = await asyngAjaxSend(
      'customer/view/credit_add_form_save',
      { values }
    );

    if (!response || response.status !== true) {
      showAlert(
        response?.msg ||
        response?.error ||
        'NO SE PUDO AGREGAR EL MONTO',
        'warning'
      );

      return;
    }

    const customerId = response.data?.customer;

    // Cerrar el formulario.
    $('#panel_view_form').html('');

    // Restaurar los botones.
    $('#credit_payment_form_open').show();
    $('#credit_add_form_open').show();

    // Recargar el panel del cliente correspondiente.
    if (customerId) {
      await customer_view_panel_load(customerId);
    }

    showAlert(
      response.msg || 'MONTO AÑADIDO EXITOSAMENTE',
      'success'
    );

  } catch (err) {
    console.error(err);

    showAlert(
      'ERROR DE COMUNICACIÓN CON EL SERVIDOR',
      'danger'
    );
  }
}

// CERRAR 
$(document).on('click', '#customer_view_close', async function (e) {
  e.preventDefault();
  await home_panel_load();
});
$(document).on('click', '#customer_view_form_close', async function (e) {
  e.preventDefault();
  $('#credit_payment_form_open').show();
  $('#credit_add_form_open').show();
  $('#panel_view_form').html('');

});