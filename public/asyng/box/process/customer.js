// async function customer_load(value = '') {
//   try {
//     const response = await asyngAjaxSend(
//       'box/controller/customer_load', { ci: value || '' }
//     );

//     if (!response.status) {
//       showAlert('ERROR', 'warning')
//     } else {
//       asyng_show_view({
//         id: 'customer_panel',
//         html: response.html,
//         effect: 'fade'
//       });
//     }

//   } catch (err) {
//     console.error(err);
//     showAlert('Error de comunicación con el servidor customer_load', 'danger');
//   }
// }
async function customer_search(value) {
  try {
    const response = await asyngAjaxSend(
      'box/controller/customer_search',
      { value: value }
    );

    if (!response.status) {
      showAlert('error', 'warning')
    } else {
      asyng_show_view({
        id: 'customer_search_panel',
        html: response.html,
        effect: 'fade'
      });
    }

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor customer_search', 'danger');
  }
}
async function customer_add(data) {
  try {
    const response = await asyngAjaxSend('box/controller/customer_add', data);

    if (!response.status) {
      showAlert(response.message || 'Error al guardar', 'danger');
      return;
    }

    showAlert(response.message || 'Cliente creado', 'success');

    // Cargar cliente en UI si existe
    if (response.data) {
      customer_panel_load(response.data.ci);
    }

    return response;

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor customer_add', 'danger');
  }
}
