(function () {
  'use strict';

  // ---- Tracking genérico (delegado) ----
  document.addEventListener('click', function (e) {
    var t = e.target && e.target.closest('.js-track');
    if (!t) return;
    var ev = t.getAttribute('data-ev') || 'click';
    if (typeof gtag === 'function') {
      try { gtag('event', ev, { event_category: 'engagement' }); } catch (_) {}
    }
  });

  // ---- Smooth scroll para anclas ----
  document.addEventListener('click', function (e) {
    var a = e.target && e.target.closest('a[href^="#"]');
    if (!a) return;
    var href = a.getAttribute('href') || '';
    if (href.length <= 1) return;
    var target = document.querySelector(href);
    if (!target) return;
    e.preventDefault();
    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  // ---- Navbar: colapso + fallback si no carga Bootstrap ----
  document.addEventListener('DOMContentLoaded', function () {
    var collapseEl = document.getElementById('navbarNav');
    var toggler = document.querySelector('.navbar-toggler');
    var bsCollapse = null;

    if (collapseEl && window.bootstrap && bootstrap.Collapse) {
      bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false });
    }

    // Cerrar al hacer clic en enlaces (móvil)
    document.querySelectorAll('.navbar-nav .nav-link').forEach(function (link) {
      link.addEventListener('click', function () {
        setTimeout(function () {
          if (!collapseEl) return;
          var isShown = collapseEl.classList.contains('show');
          if (isShown) {
            if (bsCollapse) bsCollapse.hide();
            else collapseEl.classList.remove('show');
          }
        }, 150);
      });
    });

    // Fallback toggler si no hay Bootstrap
    if (toggler) {
      toggler.addEventListener('click', function (ev) {
        if (!collapseEl) return;
        if (bsCollapse) return; // lo maneja Bootstrap data-api
        ev.preventDefault();
        collapseEl.classList.toggle('show');
      });
    }
  });

  // ---- Swiper: carrusel de marcas (robusto) ----
  (function initBrandSwiper(attempt) {
    attempt = attempt || 0;
    if (attempt > 40) return; // ~6s de reintentos
    if (typeof Swiper === 'undefined') {
      return setTimeout(function () { initBrandSwiper(attempt + 1); }, 150);
    }
    var el = document.querySelector('.brand-swiper');
    if (!el) return;
    try {
      new Swiper('.brand-swiper', {
        loop: true,
        speed: 5000,
        autoplay: { delay: 0, disableOnInteraction: false, pauseOnMouseEnter: true },
        allowTouchMove: true,
        grabCursor: true,
        slidesPerView: 5,
        spaceBetween: 24,
        navigation: { nextEl: '.brand-next', prevEl: '.brand-prev' },
        breakpoints: {
          0: { slidesPerView: 2, spaceBetween: 16 },
          480: { slidesPerView: 3, spaceBetween: 18 },
          768: { slidesPerView: 4, spaceBetween: 20 },
          1024: { slidesPerView: 5, spaceBetween: 24 }
        }
      });
    } catch (err) {
      setTimeout(function () { initBrandSwiper(attempt + 1); }, 150);
    }
  })();

})();
