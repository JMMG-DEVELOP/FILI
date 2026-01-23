
function calculateFromPrice($priceInput) {

  const name = $priceInput.attr('name'); // price_one
  const suffix = name.split('_')[1];     // one

  const cost = toNumber($('[name="cost"]').val());
  const price = toNumber($priceInput.val());

  if (cost <= 0 || price <= 0) return;

  // Inputs relacionados
  const $margin = $('[name="margin_' + suffix + '"]');
  const $percent = $('[name="x' + suffix.replace('one', '1').replace('two', '2').replace('three', '3') + '"]');

  // ================= MARGEN =================
  const margin = price - cost;
  $margin.val(formatMoney(margin > 0 ? margin : 0));

  // ================= PORCENTAJE =================
  const percent = (margin / cost) * 100;
  $percent.val(percent > 0 ? percent.toFixed(2) : 0);

  // ================= EFECTO VISUAL =================
  asyngVisualEffects([$priceInput, $margin, $percent]);
}
function calculateFromMargin($marginInput) {

  const name = $marginInput.attr('name');   // margin_one
  const suffix = name.split('_')[1];        // one

  const cost = toNumber($('[name="cost"]').val());
  const margin = toNumber($marginInput.val());

  if (cost <= 0 || margin < 0) return;

  // Inputs relacionados
  const $price = $('[name="price_' + suffix + '"]');
  const $percent = $('[name="x' + suffix.replace('one', '1').replace('two', '2').replace('three', '3') + '"]');

  // ================= PRECIO =================
  const price = cost + margin;
  $price.val(formatMoney(price));

  // ================= PORCENTAJE =================
  const percent = (margin / cost) * 100;
  $percent.val(percent > 0 ? percent.toFixed(2) : 0);

  // ================= EFECTO VISUAL =================
  asyngVisualEffects([$marginInput, $price, $percent]);
}
function indexToWord(index) {
  const map = {
    '1': 'one',
    '2': 'two',
    '3': 'three'
  };
  return map[index];
}
function calculateFromPercent($percentInput) {

  const name = $percentInput.attr('name'); // x1, x2, x3
  const index = name.replace('x', '');

  const cost = toMoneyNumber($('[name="cost"]').val());
  const percent = toPercentNumber($percentInput.val());

  if (cost <= 0 || percent < 0) return;

  const word = indexToWord(index);

  const $margin = $('[name="margin_' + word + '"]');
  const $price = $('[name="price_' + word + '"]');

  // ================= CÁLCULOS CORRECTOS =================
  const margin = cost * (percent / 100);
  const price = cost + margin;

  // ================= ESCRIBIR =================
  $margin.val(formatMoney(margin));
  $price.val(formatMoney(price));

  // ================= EFECTO VISUAL =================
  asyngVisualEffects([$percentInput, $margin, $price]);
}
function recalculateFromCost() {

  const cost = toMoneyNumber($('[name="cost"]').val());
  if (cost <= 0) return;

  const levels = [
    { price: 'price_one', margin: 'margin_one', percent: 'x1' },
    { price: 'price_two', margin: 'margin_two', percent: 'x2' },
    { price: 'price_three', margin: 'margin_three', percent: 'x3' }
  ];

  let affected = [];

  levels.forEach(level => {

    const $price = $('[name="' + level.price + '"]');
    const $margin = $('[name="' + level.margin + '"]');
    const $percent = $('[name="' + level.percent + '"]');

    if (!$price.length) return;

    const priceValue = toMoneyNumber($price.val());
    if (priceValue <= 0) return;

    // ================= MARGEN =================
    const margin = priceValue - cost;
    if (margin < 0) return;

    $margin.val(formatMoney(margin));

    // ================= PORCENTAJE =================
    const percent = (margin / cost) * 100;
    $percent.val(percent.toFixed(1));

    affected.push($margin, $percent);
  });

  if (affected.length) {
    asyngVisualEffects(affected);
  }
}


$(document).on('keydown', '.money[name^="price_"]', function (e) {

  if (e.key === 'Enter') {
    e.preventDefault();
    calculateFromPrice($(this));
  }
});

$(document).on('keydown', '.money[name^="margin_"]', function (e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    calculateFromMargin($(this));
  }
});

$(document).on('keydown', '.percent[name^="x"]', function (e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    calculateFromPercent($(this));
  }
});
$(document).on('keydown', '[name="cost"]', function (e) {

  if (e.key === 'Enter') {
    e.preventDefault();
    recalculateFromCost();
  }

});

// FORMATEO DE INPUT CODE
$(document).on('input', '#code', function () {

  let raw = $(this).val().trim();

  // Eliminar ceros a la izquierda
  raw = raw.replace(/^0+/, '');

  // Evitar vacío
  if (raw === '') {
    raw = '0';
  }

  // Rellenar hasta 7 dígitos
  const formatted = raw.length <= 7
    ? raw.padStart(7, '0')
    : raw;

  // 🔑 Asignar al input
  $(this).val(formatted);
});

$(document).on('keydown', '#code', async function (e) {

  if (e.key === 'Enter') {
    e.preventDefault();
    let data = {
      code: $('#code').val()
    };

    const response = await asyngAjaxSend('products/products/product_save_verify', data);

    if (!response.status && response.error === 'code_exists') {
      showAlert(response.message, 'danger');
      return;
    } else if (!response.status && response.error === 'code_empty') {
      showAlert(response.message, 'danger');
    }
    else if (response.status) {
      showAlert(response.message, 'success');
    }

  }

});

