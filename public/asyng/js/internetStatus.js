(function () {

  'use strict';

  // ============================================================
  // CONFIGURACIÓN
  // ============================================================

  const CHECK_INTERVAL = 10000; // 10 segundos
  const TIMEOUT = 4000;         // 4 segundos

  const INTERNET_URL = 'https://www.google.com/generate_204';

  let checking = false;
  let timer = null;

  const statusElement = document.getElementById(
    'fili_connection_status'
  );

  if (!statusElement) return;

  const SERVER_URL = statusElement.dataset.serverUrl;

  // ============================================================
  // MOSTRAR ESTADO
  // ============================================================

  function setStatus(type, message, description) {

    const statuses = {
      online: {
        class: 'badge badge-success',
        icon: 'fas fa-wifi'
      },

      noInternet: {
        class: 'badge badge-warning',
        icon: 'fas fa-cloud'
      },

      serverError: {
        class: 'badge badge-danger',
        icon: 'fas fa-server'
      },

      offline: {
        class: 'badge badge-dark',
        icon: 'fas fa-wifi'
      },

      checking: {
        class: 'badge badge-secondary',
        icon: 'fas fa-sync-alt'
      }
    };

    const config = statuses[type];

    statusElement.className = config.class;

    statusElement.innerHTML = `
            <i class="${config.icon}"></i>
            ${message}
        `;

    statusElement.title = description || message;
  }

  // ============================================================
  // COMPROBAR INTERNET
  // ============================================================

  async function checkInternet() {

    const controller = new AbortController();

    const timeout = setTimeout(
      () => controller.abort(),
      TIMEOUT
    );

    try {

      const response = await fetch(INTERNET_URL, {
        method: 'GET',
        mode: 'no-cors',
        cache: 'no-store',
        signal: controller.signal
      });

      // Con no-cors, la respuesta será opaque.
      // Si fetch resuelve, consideramos que hay conectividad.

      return true;

    } catch (error) {

      return false;

    } finally {

      clearTimeout(timeout);

    }
  }

  // ============================================================
  // COMPROBAR SERVIDOR FILI
  // ============================================================

  async function checkServer() {

    const controller = new AbortController();

    const timeout = setTimeout(
      () => controller.abort(),
      TIMEOUT
    );

    try {

      const response = await fetch(SERVER_URL, {

        method: 'GET',

        cache: 'no-store',

        credentials: 'same-origin',

        headers: {
          'Accept': 'application/json'
        },

        signal: controller.signal

      });

      if (!response.ok) {
        return false;
      }

      const data = await response.json();

      return data.status === 'ok' && data.server === true;

    } catch (error) {

      return false;

    } finally {

      clearTimeout(timeout);

    }
  }

  // ============================================================
  // ACTUALIZAR CONEXIONES
  // ============================================================

  async function checkConnections() {

    if (checking) return;

    checking = true;

    try {

      // Sin conexión de red según el navegador
      if (!navigator.onLine) {

        setStatus(
          'offline',
          'Sin conexión',
          'El dispositivo no tiene conexión de red.'
        );

        return;
      }

      // Comprobar ambas conexiones simultáneamente
      const [internet, server] = await Promise.all([
        checkInternet(),
        checkServer()
      ]);

      // Internet y servidor disponibles
      if (internet && server) {

        setStatus(
          'online',
          'En línea',
          'Internet y servidor FILI disponibles.'
        );

      }

      // Internet desconectado, pero FILI disponible
      else if (!internet && server) {

        setStatus(
          'noInternet',
          'Sin Internet',
          'Servidor FILI disponible. Puedes seguir trabajando localmente.'
        );

      }

      // Internet disponible, pero servidor no responde
      else if (internet && !server) {

        setStatus(
          'serverError',
          'Servidor no disponible',
          'Hay Internet, pero el servidor FILI no responde.'
        );

      }

      // Ninguna conexión disponible
      else {

        setStatus(
          'offline',
          'Sin conexión',
          'No se puede acceder a Internet ni al servidor FILI.'
        );

      }

    } finally {

      checking = false;

    }
  }

  // ============================================================
  // EVENTOS DEL NAVEGADOR
  // ============================================================

  window.addEventListener('online', function () {

    checkConnections();

  });

  window.addEventListener('offline', function () {

    setStatus(
      'offline',
      'Sin conexión',
      'Se perdió la conexión de red.'
    );

  });

  // ============================================================
  // INICIAR MONITOREO
  // ============================================================

  function init() {

    setStatus(
      'checking',
      'Verificando...',
      'Comprobando Internet y servidor FILI.'
    );

    checkConnections();

    timer = setInterval(
      checkConnections,
      CHECK_INTERVAL
    );

  }

  if (document.readyState === 'loading') {

    document.addEventListener('DOMContentLoaded', init);

  } else {

    init();

  }

})();