<div class="col-md-12">

  <div class="form-row ">


    <!-- Tipo de adición -->
    <div class="col-md-2 mb-2 mt-4">
      <div class="custom-control custom-checkbox">
        <input type="checkbox" name="add_cart_type" id="add_cart_type" class="custom-control-input">
        <label class="custom-control-label" for="add_cart_type">
          Verificar
        </label>
      </div>
    </div>

    <!-- Selección manual del precio -->
    <div class="col-md-2 mb-2 mt-4">
      <div class="custom-control custom-radio">
        <input type="radio" name="price_tier" id="all_price_one" value="1" class="custom-control-input">
        <label class="custom-control-label" for="all_price_one">
          Precio 1
        </label>
      </div>
    </div>

    <div class="col-md-2 mb-2 mt-4">
      <div class="custom-control custom-radio">
        <input type="radio" name="price_tier" id="all_price_two" value="2" class="custom-control-input">
        <label class="custom-control-label" for="all_price_two">
          Precio 2
        </label>
      </div>
    </div>

    <div class="col-md-2 mb-2 mt-4">
      <div class="custom-control custom-radio">
        <input type="radio" name="price_tier" id="all_price_three" value="3" class="custom-control-input">
        <label class="custom-control-label" for="all_price_three">
          Precio 3
        </label>
      </div>
    </div>

    <!-- Cantidad -->
    <div class="col-md-1 mb-2  mt-4">
      <!-- <label for="product_cant">Cant.</label> -->
      <input type="text" class="form-control" name="product_cant" id="product_cant" value="1">
    </div>

    <!-- Otro precio manual -->
    <div class="col-md-2 mb-2 mt-4">
      <input type="text" class="form-control" name="product_price_input" id="product_price_input"
        placeholder="Otro precio">
    </div>

    <!-- Quitar -->
    <div class="col-md-1 mb-1 mt-3">
      <button type="button" id="remove_cart" class="btn btn-danger form-control">
        <i class="fas fa-times"></i>
      </button>
    </div>


  </div>


</div>