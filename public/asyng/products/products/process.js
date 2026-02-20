
function calculatePriceCard() {
  // Inputs base
  const costValue = toMoneyNumber($('[name="cost"]').val());
  const priceOne = toMoneyNumber($('[name="price_one"]').val());
  const percentExtra = toMoneyNumber($('#card_percent_cant').val());

  if (costValue <= 0 || priceOne <= 0) return;

  // 1️⃣ Calcular price_card sumando % a price_one
  const priceCard = priceOne + (priceOne * percentExtra / 100);

  // 2️⃣ Calcular margin_card desde costo
  const marginCard = priceCard - costValue;

  // 3️⃣ Calcular discount (diferencia contra price_one)
  const discount = priceCard - priceOne;

  // 4️⃣ Escribir valores
  $('[name="price_card"]').val(priceCard.toFixed(0)).trigger('input');
  $('[name="margin_card"]').val(marginCard.toFixed(0)).trigger('input');
  $('[name="discount_card"]').val(discount.toFixed(0)).trigger('input');

  // 5️⃣ Efecto visual
  asyngVisualEffects([
    $('#card_percent_cant'),
    $('[name="price_card"]'),
    $('[name="margin_card"]'),
    $('[name="discount_card"]')
  ]);

}
function calculateFromPrice($priceInput) {

  const name = $priceInput.attr('name');
  const suffix = name.split('_')[1];

  const cost = toMoneyNumber($('[name="cost"]').val());
  const price = toMoneyNumber($priceInput.val());

  if (cost <= 0 || price <= 0) return;

  const $margin = $('[name="margin_' + suffix + '"]');
  const $percent = $('[name="x' + suffix
    .replace('one', '1')
    .replace('two', '2')
    .replace('three', '3') + '"]');

  const margin = price - cost;
  const percent = (margin / cost) * 100;

  $margin.val(margin).trigger('input');
  $percent.val(percent.toFixed(1)).trigger('input');

  asyngVisualEffects([$priceInput, $margin, $percent]);
  calculatePriceCard();

}

function calculateFromMargin($marginInput) {

  const name = $marginInput.attr('name');
  const suffix = name.split('_')[1];

  const cost = toMoneyNumber($('[name="cost"]').val());
  const margin = toMoneyNumber($marginInput.val());

  if (cost <= 0 || margin < 0) return;

  const $price = $('[name="price_' + suffix + '"]');
  const $percent = $('[name="x' + suffix.replace('one', '1').replace('two', '2').replace('three', '3') + '"]');

  const price = cost + margin;
  const percent = (margin / cost) * 100;

  $price.val(price).trigger('input');
  $percent.val(percent.toFixed(1)).trigger('input');

  asyngVisualEffects([$marginInput, $price, $percent]);
  calculatePriceCard();
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

  const index = $percentInput.attr('name').replace('x', '');
  const cost = toMoneyNumber($('[name="cost"]').val());
  const percent = toPercentNumber($percentInput.val());

  if (cost <= 0 || percent < 0) return;

  const word = indexToWord(index);

  const margin = cost * (percent / 100);
  const price = cost + margin;

  $('[name="margin_' + word + '"]').val(margin).trigger('input');
  $('[name="price_' + word + '"]').val(price).trigger('input');

  asyngVisualEffects([$percentInput]);
  calculatePriceCard();
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

    // 👉 ESCRIBIR NÚMERO LIMPIO
    $margin.val(margin).trigger('input');

    // ================= PORCENTAJE =================
    const percent = (margin / cost) * 100;
    $percent.val(percent.toFixed(1)).trigger('input');

    affected.push($margin, $percent);
  });

  if (affected.length) {
    asyngVisualEffects(affected);
  }
  calculatePriceCard();
}


async function code_verify(data) {
  const response = await asyngAjaxSend('products/products/product_save_verify', data);

  if (!response.status && response.error === 'code_exists') {
    showAlert(response.message, 'danger');
    return response.error;
  } else if (!response.status && response.error === 'code_empty') {
    showAlert(response.message, 'danger');
    return response.error;
  }
  else if (response.status) {
    showAlert(response.message, 'success');
    return response.error;
  }
}

function asyng_product_form(product) {
  $('#products_form #code').val(product.code);
  $('#products_form #description').val(product.description);
  $('#products_form #sections').val(product.section_id).trigger('change.select2');
  $('#products_form #brands').val(product.brand_id).trigger('change');
  $('#products_form #sales').val(product.sales_id).trigger('change');

  $('#products_form #stock_1').val(product.stock[1]);
  $('#products_form #edit_stock_1').val(0);

  $('#products_form #stock_2').val(product.stock[2]);
  $('#products_form #edit_stock_2').val(0);

  $('#products_form #cost').val(product.cost);
  $('#products_form #other_cost').val(product.other_cost);
  $('#products_form #price_one').val(product.prices.price_one);
  $('#products_form #price_two').val(product.prices.price_two);

  $('#products_form #price_three').val(product.prices.price_three);
  recalculateFromCost()
  $('#products_form #cant_one').val(product.cant_one);
  $('#products_form #cant_two').val(product.cant_two);
  $('#products_form #cant_three').val(product.cant_three);

  $('#products_form #iva').val(product.iva_id).trigger('change');
}