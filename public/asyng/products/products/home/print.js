
$(document).on(
  'click',
  '.print_barcode',
  async function (e) {

    e.preventDefault();

    const button = $(this);

    if (button.prop('disabled')) {
      return;
    }

    const code = $('#shopp_code').val();

    const quantity = $('#cant_code_print').val();

    const originalHTML = button.html();

    try {

      button
        .prop('disabled', true)
        .html(
          '<i class="fas fa-spinner fa-spin"></i> Imprimiendo...'
        );

      const result = await window.imprimirBarcode(
        code,
        quantity
      );

      if (result.status === true) {

        showAlert(
          'TRABAJO ENVIADO A LA IMPRESORA. CANTIDAD: '
          + quantity,
          'success'
        );

      }

    } finally {

      button
        .prop('disabled', false)
        .html(originalHTML);

    }

  }
);
$(document).on(
  'click',
  '.print_name',
  async function (e) {

    e.preventDefault();

    const button = $(this);

    if (button.prop('disabled')) {
      return;
    }

    const name = $('#shopp_description').val();


    const originalHTML = button.html();

    try {

      button
        .prop('disabled', true)
        .html(
          '<i class="fas fa-spinner fa-spin"></i> Imprimiendo...'
        );

      const result = await window.imprimirName(
        name
      );

      if (result.status === true) {

        showAlert(
          'ENVIANDO A LA IMPRESORA',
          'success'
        );

      }

    } finally {

      button
        .prop('disabled', false)
        .html(originalHTML);

    }

  }
);
$(document).on(
  'click',
  '.print_label',
  async function (e) {

    e.preventDefault();

    const button = $(this);

    if (button.prop('disabled')) {
      return;
    }

    const code = $('#shopp_code').val();
    const description = $('#shopp_description').val();

    const price_1 = $('#shopp_price_one').val();
    const price_2 = $('#shopp_price_two').val();
    const cant_2 = $('#shopp_cant_two').val();

    const quantity = Number($('#cant_code_print').val());

    const originalHTML = button.html();

    try {

      button
        .prop('disabled', true)
        .html(
          '<i class="fas fa-spinner fa-spin"></i> Imprimiendo...'
        );

      const result = await window.imprimirLabel(
        code,
        description,
        price_1,
        price_2,
        cant_2,
        quantity
      );

      if (result.status === true) {

        showAlert(
          'TRABAJO ENVIADO A LA IMPRESORA. CANTIDAD: ' + quantity,
          'success'
        );

      }

    } finally {

      button
        .prop('disabled', false)
        .html(originalHTML);

    }

  }
);