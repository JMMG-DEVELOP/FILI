function product_add_open() {
  $.post(
    BASE_URL + '/products/products/product_open',
    {
      [csrfName]: csrfHash
    },
    function (response) {

      if (response.status) {
        open_hide('#products_form', '#products_panel', response.form, function () {

          // ⏳ esperar render real
          requestAnimationFrame(() => {
            asyngMoneyMask('#products_form .money');
            asyngPercentMask('#products_form .percent');
            asyngNumericStock('#products_form .stock');

            $("#operation_type").val(response.data.type);
          });
        });

      }

      csrfHash = response.csrfHash;
    },
    'json'
  );
}


function product_add_save() {

}


$(document).on('click', '.product_cancel', function (e) {
  e.preventDefault();
  cancel_open('#products_form', '#products_panel');
});

//Abrir Modal de Registrar Productos
$(document).on('click', '.product_add', function (e) {
  e.preventDefault();
  product_add_open();
});
$(document).on('click', '.product_send', async function (e) {
  e.preventDefault();

  // 1️⃣ Validar formulario
  if (!validateForm('#product_form')) {
    return;
  }

  // 2️⃣ Obtener datos del form (array)
  const dataArray = asyngFormData('#product_form');

  // 3️⃣ Convertir array → objeto (OPCIÓN PRO)
  const formData = Object.fromEntries(
    dataArray.map(item => [item.name, item.value])
  );

  // 4️⃣ Determinar operación
  if (formData.operation_type === 'add') {
    try {
      const response = await asyngAjaxSend(
        'products/products/product_save',
        dataArray // ⚠️ seguimos enviando el array
      );

      if (!response.status && response.error === 'code_exists') {
        showAlert(response.message, 'danger');
        return;
      }

      if (!response.status) {
        showAlert(
          response.message || 'Error al guardar el producto',
          'danger'
        );
        return;
      }

      // ✅ Éxito
      showAlert(response.message, 'success');

    } catch (err) {
      console.error(err);
      showAlert('Error de comunicación con el servidor', 'danger');
    }
  }
});


// $(document).on('click', '.product_send', async function (e) {
//   e.preventDefault();

//   if (!validateForm('#product_form')) {
//     return;
//   }
//   const data = asyngFormData('#product_form');
//   if (data.operation_type === 'add') {
//     try {

//       const response = await asyngAjaxSend('products/products/product_save', data);

//       if (!response.status && response.error === 'code_exists') {
//         showAlert(response.message, 'danger');
//         return;
//       } else

//         if (!response.status) {
//           showAlert(response.message || 'Error al guardar el producto', 'danger');
//           return;
//         } else {
//           showAlert(response.message, 'success');
//         }

//     } catch (err) {
//       showAlert('Error de comunicación con el servidor', 'danger');
//     }
//   }

// });


