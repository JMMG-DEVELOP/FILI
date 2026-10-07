$(document).on('click', '#sequence_form_new_open', async function (e) {
  e.preventDefault();
  await point_form_new_open();
});

$(document).on('click', '#sequence_form_new_save', async function (e) {
  e.preventDefault();
  await point_form_new_save();
});

$(document).on(
  'click',
  '.invoice_sequence_delete_open, .document_sequence_delete_open',
  async function (e) {

    e.preventDefault();

    const id = $(this).attr('data-id');
    const name = $(this).attr('data-name');
    const type = $(this).attr('data-type');
    const sequenceType = $(this).attr('data-sequence-type');

    await sequence_delete_open(
      id,
      name,
      type,
      sequenceType
    );
  }
);

$(document).on(
  'click',
  '#delete_sequence',
  async function (e) {

    e.preventDefault();

    const id = $(this).attr('data-id');
    const sequenceType = $(this).attr('data-sequence-type');

    if (!id) {

      showAlert(
        'NO SE RECIBIO EL ID',
        'warning'
      );

      return;
    }

    if (!sequenceType) {

      showAlert(
        'NO SE RECIBIO EL TIPO DE SECUENCIA',
        'warning'
      );

      return;
    }

    await sequence_delete_save(
      id,
      sequenceType
    );
  }
);
$(document).on('click', '.sequence_delete_close', async function (e) {
  e.preventDefault();
  await sequence_panel_load();
});

$(document).on('click', '#sequence_form_close', async function (e) {
  e.preventDefault();
  await sequence_panel_load();
});

