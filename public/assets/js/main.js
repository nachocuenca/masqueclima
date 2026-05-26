/* main.js — utilidades front y comportamiento genérico del sitio */

(function () {
  'use strict';

  // Tracking básico de clics/submit (opcional)
  document.addEventListener('click', function (ev) {
    var t = ev.target.closest('.js-track');
    if (!t) return;
    // Ejemplo: data-ev="whatsapp_click"
    var evName = t.getAttribute('data-ev') || 'click';
    // console.log('[track]', evName);
  });

  // Aceptación de cookies (ejemplo simple)
  var cookieBanner = document.querySelector('.cookie-banner');
  var acceptBtn = document.querySelector('.js-accept-cookies');
  if (cookieBanner && acceptBtn) {
    try {
      var accepted = localStorage.getItem('cookiesAccepted') === '1';
      if (!accepted) cookieBanner.classList.remove('d-none');
      acceptBtn.addEventListener('click', function(){
        localStorage.setItem('cookiesAccepted', '1');
        cookieBanner.classList.add('d-none');
      });
    } catch(e) {}
  }

  // Validación mínima del formulario del modal (HTML5 + clases)
  document.addEventListener('submit', function (ev) {
    var form = ev.target;
    if (!form.classList.contains('js-track-form')) return;
    if (!form.checkValidity()) {
      ev.preventDefault();
      ev.stopPropagation();
      form.classList.add('was-validated');
      return;
    }
    // Deja que el submit haga POST normal al handler (queremos redirección y flashes)
  }, true);

})();
