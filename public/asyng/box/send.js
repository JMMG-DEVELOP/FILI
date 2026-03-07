async function customer_add(data) {

  try {
    const response = await asyngAjaxSend(
      'box/controller/customer_add', data

    );

    if (!response.status) {
      showAlert(response.message || 'Error al GUARDAR',
        'danger');
      return;
    } else {
      showAlert(response.message || 'EXITO', 'success');
      customer_load(response.data.ci);
      return response;
    }

  } catch (err) {
    console.error(err);
    showAlert('Error de comunicación con el servidor', 'danger');
  }

}
