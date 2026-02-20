/**
 * SoundManager POS
 * -----------------
 * Sonidos rápidos sin MP3 usando Web Audio API
 */
// SoundManager.success(); // venta OK
// SoundManager.error();   // error
// SoundManager.warning(); // alerta
// SoundManager.payment(); // pago confirmado
// SoundManager.scan();    // lector código
// SoundManager.click();   // click UI

const SoundManager = (() => {

  let ctx = null;
  let enabled = true;

  // Inicializar contexto solo cuando se usa
  function getCtx() {
    if (!ctx) {
      ctx = new (window.AudioContext || window.webkitAudioContext)();
    }
    return ctx;
  }

  function tone({
    frequency = 440,
    duration = 0.1,
    type = "sine",
    volume = 0.3,
    time = 0
  }) {
    if (!enabled) return;

    const context = getCtx();
    const osc = context.createOscillator();
    const gain = context.createGain();

    osc.type = type;
    osc.frequency.value = frequency;

    osc.connect(gain);
    gain.connect(context.destination);

    const now = context.currentTime + time;

    gain.gain.setValueAtTime(volume, now);
    gain.gain.exponentialRampToValueAtTime(
      0.001,
      now + duration
    );

    osc.start(now);
    osc.stop(now + duration);
  }

  /* =========================
     SONIDOS
  ==========================*/

  function success() {
    tone({ frequency: 700, duration: 0.08, type: "sine" });
    tone({ frequency: 1000, duration: 0.12, type: "sine", time: 0.1 });
  }

  function error() {
    tone({ frequency: 400, duration: 0.24, type: "square" });
    tone({ frequency: 250, duration: 0.30, type: "square", time: 0.30 });
  }

  function warning() {
    tone({ frequency: 600, duration: 0.1, type: "triangle" });
    tone({ frequency: 600, duration: 0.1, type: "triangle", time: 0.18 });
  }

  function payment() {
    tone({ frequency: 900, duration: 0.07 });
    tone({ frequency: 1200, duration: 0.1, time: 0.08 });
    tone({ frequency: 1500, duration: 0.12, time: 0.16 });
  }

  function scan() {
    tone({ frequency: 1100, duration: 0.05, type: "square" });
  }

  function click() {
    tone({ frequency: 800, duration: 0.03 });
  }
  function beep(duration = 200, frequency = 800, volume = 0.5) {

    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioCtx.createOscillator();
    const gainNode = audioCtx.createGain();

    oscillator.connect(gainNode);
    gainNode.connect(audioCtx.destination);

    oscillator.frequency.value = frequency;
    gainNode.gain.value = volume;

    oscillator.start();

    setTimeout(() => {
      oscillator.stop();
    }, duration);
  }

  /* =========================
     CONTROL
  ==========================*/

  function mute() {
    enabled = false;
  }

  function unmute() {
    enabled = true;
  }

  function toggle() {
    enabled = !enabled;
  }

  function isEnabled() {
    return enabled;
  }

  /* =========================
     API
  ==========================*/

  return {
    success,
    error,
    warning,
    payment,
    scan,
    click,
    beep,
    mute,
    unmute,
    toggle,
    isEnabled
  };

})();
