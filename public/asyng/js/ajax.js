// ================================
// LOADER GLOBAL BLINDADO
// ================================
let __ASYNG_LOADER_COUNT = 0;

function asyngLoader(show = true) {

  if (show) {
    __ASYNG_LOADER_COUNT++;

    if ($('#asyng-loader').length === 0) {
      $('body').append(`
        <div id="asyng-loader" style="
          position: fixed;
          top: 0; left: 0;
          width: 100%; height: 100%;
          background: rgba(255,255,255,0.7);
          z-index: 9999;
          display: flex;
          justify-content: center;
          align-items: center;
        ">
          <div class="spinner-border text-primary" role="status"></div>
        </div>
      `);
    }

  } else {
    __ASYNG_LOADER_COUNT--;

    if (__ASYNG_LOADER_COUNT <= 0) {
      __ASYNG_LOADER_COUNT = 0;
      $('#asyng-loader').remove();
    }
  }
}


// ===== SWEETALERT HELPERS (Opcional) =====
function asyngSuccess(msg) {
  if (window.Swal) {
    Swal.fire("Éxito", msg, "success");
  } else {
    alert(msg);
  }
}

function asyngError(msg) {
  if (window.Swal) {
    Swal.fire("Error", msg, "error");
  } else {
    alert(msg);
  }
}

function asyngInfo(msg) {
  if (window.Swal) {
    Swal.fire("Info", msg, "info");
  } else {
    alert(msg);
  }
}


// Preparación de formulario para envio por ajax
function asyngFormData(form) {
  let formData = $(form).serializeArray();

  formData.push({
    name: csrfName,
    value: csrfHash
  });

  return formData;
}

// Envio de datos por ajax

function asyngAjaxSend(url, data = {}, useLoader = true) {

  return new Promise((resolve, reject) => {

    if (useLoader) asyngLoader(true);

    // CSRF seguro
    if (Array.isArray(data)) {
      data.push({ name: csrfName, value: csrfHash });
    } else if (!(data instanceof FormData)) {
      data[csrfName] = csrfHash;
    }

    $.ajax({
      url: BASE_URL + "/" + url,
      type: "POST",
      dataType: "json",
      data: data,
      processData: !(data instanceof FormData),
      contentType: (data instanceof FormData)
        ? false
        : "application/x-www-form-urlencoded; charset=UTF-8",

      success: function (response) {

        // Actualizar CSRF
        if (response.csrfName && response.csrfHash) {
          csrfName = response.csrfName;
          csrfHash = response.csrfHash;

          $('meta[name="csrf_token_name"]').attr("content", csrfName);
          $('meta[name="csrf_token_hash"]').attr("content", csrfHash);
        }

        resolve(response);
      },

      error: function (xhr) {
        console.error('AJAX ERROR:', xhr);
        reject(xhr);
      },

      complete: function () {
        if (useLoader) asyngLoader(false);
      }
    });

  }).catch(err => {

    // 🔐 BLINDAJE FINAL (por si algo rompe antes del complete)
    asyngLoader(false);
    throw err;

  });
}




