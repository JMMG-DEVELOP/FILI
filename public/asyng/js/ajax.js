// ===== LOADER GLOBAL (Opcional) =====
function asyngLoader(show = true) {
  if (show) {
    if ($("#asyng-loader").length === 0) {
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
                    <div class="spinner-border" role="status"></div>
                </div>
            `);
    }
  } else {
    $("#asyng-loader").remove();
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

  // Insertar CSRF correctamente
  formData.push({
    name: csrfName,
    value: csrfHash
  });

  return formData;
}

// Envio de datos por ajax

function asyngAjaxSend(url, data = {}, useLoader = true) {
  return new Promise(function (resolve, reject) {

    if (useLoader) asyngLoader(true);

    // Si data es array → serializeArray → agregar CSRF
    if (Array.isArray(data)) {
      data.push({ name: csrfName, value: csrfHash });
    }

    // Si es objeto normal
    else if (!(data instanceof FormData)) {
      data[csrfName] = csrfHash;
    }

    $.ajax({
      url: baseUrl + "/" + url,
      type: "POST",
      dataType: "json",
      data: data,
      processData: !(data instanceof FormData),
      contentType: (data instanceof FormData)
        ? false
        : "application/x-www-form-urlencoded; charset=UTF-8",

      success: function (response) {

        // Actualizar token
        if (response.csrfName && response.csrfHash) {
          csrfName = response.csrfName;
          csrfHash = response.csrfHash;

          $('meta[name="csrf_token_name"]').attr("content", csrfName);
          $('meta[name="csrf_token_hash"]').attr("content", csrfHash);
        }

        resolve(response);
      },

      error: function (xhr) {
        reject(xhr.responseText);
      },

      complete: function () {
        if (useLoader) asyngLoader(false);
      }
    });
  });
}

