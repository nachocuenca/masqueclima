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

  // ---- Tracking submit ----
  document.addEventListener('submit', function (e) {
    var f = e.target && e.target.closest('.js-track-form');
    if (!f) return;
    var ev = f.getAttribute('data-ev') || 'form_submit';
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

  // ---- Hero background video ----
  document.addEventListener('DOMContentLoaded', function () {
    var desktop = !window.matchMedia || window.matchMedia('(min-width: 992px)').matches;

    document.querySelectorAll('.hero-video').forEach(function (video) {
      var show = function () { video.classList.add('is-ready'); };
      var source = video.querySelector('source[data-src]');

      video.addEventListener('loadeddata', show, { once: true });
      video.addEventListener('canplay', show, { once: true });

      if (!desktop) return;

      if (source && !source.src) source.src = source.getAttribute('data-src') || '';
      video.muted = true;
      video.autoplay = true;
      video.playsInline = true;

      try {
        video.load();
        var play = video.play();
        if (play && play.catch) play.catch(function () {});
      } catch (_) {}

      setTimeout(show, 1000);
    });
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

  // ---- Google reviews carousel (progressive enhancement) ----
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-google-reviews-carousel]').forEach(function (root) {
      var cards = Array.prototype.slice.call(root.querySelectorAll('.google-review-card'));
      if (!cards.length) return;

      var track = root.querySelector('.google-reviews-track');
      var dotsHost = root.querySelector('.google-review-dots');
      var visible = getVisibleCount();
      var pages = Math.ceil(cards.length / visible);
      var current = 0;
      var timer = null;
      var resizeTimer = null;
      var transitionTimer = null;
      var transitionDelay = 220;
      var reducedMotionQuery = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : null;
      var reducedMotion = !!(reducedMotionQuery && reducedMotionQuery.matches);
      var interval = parseInt(root.getAttribute('data-interval') || '6000', 10);

      root.classList.add('is-ready');

      function getVisibleCount() {
        if (window.matchMedia && window.matchMedia('(max-width: 767.98px)').matches) return 1;
        if (window.matchMedia && window.matchMedia('(max-width: 991.98px)').matches) return 2;
        return Math.max(1, parseInt(root.getAttribute('data-visible') || '3', 10) || 3);
      }

      function applyPage() {
        var start = current * visible;
        var end = start + visible;
        cards.forEach(function (card, idx) {
          if (idx >= start && idx < end) {
            card.classList.remove('google-review-card--hidden');
          } else {
            card.classList.add('google-review-card--hidden');
          }
        });
      }

      function updateDots() {
        if (dotsHost) {
          dotsHost.querySelectorAll('.google-review-dot').forEach(function (dot, idx) {
            var active = idx === current;
            dot.classList.toggle('is-active', active);
            dot.setAttribute('aria-current', active ? 'true' : 'false');
          });
        }
      }

      function showPage(pageIndex, immediate) {
        if (pages < 1) pages = 1;
        var next = Math.max(0, Math.min(pageIndex, pages - 1));
        var shouldAnimate = !immediate && !reducedMotion && track && next !== current;

        current = next;
        updateDots();

        clearTimeout(transitionTimer);
        if (!shouldAnimate) {
          if (track) track.classList.remove('is-transitioning');
          applyPage();
          return;
        }

        track.classList.add('is-transitioning');
        transitionTimer = setTimeout(function () {
          applyPage();
          if (window.requestAnimationFrame) {
            window.requestAnimationFrame(function () {
              track.classList.remove('is-transitioning');
            });
          } else {
            track.classList.remove('is-transitioning');
          }
        }, transitionDelay);
      }

      function nextPage() {
        showPage((current + 1) % pages, false);
      }

      function buildDots() {
        if (!dotsHost) return;
        dotsHost.innerHTML = '';
        if (pages <= 1) return;
        var labelTemplate = dotsHost.getAttribute('data-dot-label') || 'Slide %s';
        for (var i = 0; i < pages; i++) {
          var dot = document.createElement('button');
          dot.type = 'button';
          dot.className = 'google-review-dot' + (i === 0 ? ' is-active' : '');
          dot.setAttribute('aria-label', labelTemplate.replace('%s', String(i + 1)));
          dot.setAttribute('aria-current', i === 0 ? 'true' : 'false');
          dot.addEventListener('click', (function (idx) {
            return function () {
              stopRotation();
              showPage(idx, false);
              startRotation();
            };
          })(i));
          dotsHost.appendChild(dot);
        }
      }

      function stopRotation() {
        if (!timer) return;
        clearInterval(timer);
        timer = null;
      }

      function startRotation() {
        if (reducedMotion || timer || pages <= 1) return;
        timer = setInterval(nextPage, Math.max(5000, interval));
      }

      function syncLayout() {
        var nextVisible = getVisibleCount();
        if (nextVisible !== visible) {
          visible = nextVisible;
          pages = Math.ceil(cards.length / visible);
          current = Math.min(current, Math.max(0, pages - 1));
          buildDots();
        }
        showPage(current, true);
      }

      if (reducedMotionQuery && reducedMotionQuery.addEventListener) {
        reducedMotionQuery.addEventListener('change', function (event) {
          reducedMotion = event.matches;
          if (reducedMotion && track) {
            clearTimeout(transitionTimer);
            track.classList.remove('is-transitioning');
            applyPage();
          }
        });
      }

      root.addEventListener('mouseenter', stopRotation);
      root.addEventListener('mouseleave', startRotation);
      root.addEventListener('focusin', stopRotation);
      root.addEventListener('focusout', startRotation);
      window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(syncLayout, 120);
      });

      buildDots();
      syncLayout();
      startRotation();
    });
  });

  // ---- Keep the floating WhatsApp button from covering reviews ----
  document.addEventListener('DOMContentLoaded', function () {
    var wa = document.querySelector('.whatsapp-floating, .btn-whatsapp-pulse');
    var sections = Array.prototype.slice.call(document.querySelectorAll('.google-reviews-section'));
    if (!wa || !sections.length) return;

    var ticking = false;

    function sync() {
      ticking = false;
      var viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
      var active = sections.some(function (section) {
        var rect = section.getBoundingClientRect();
        return rect.top < viewportHeight - 80 && rect.bottom > 80;
      });

      wa.classList.toggle('whatsapp-floating--reviews-hidden', active);
    }

    function requestSync() {
      if (ticking) return;
      ticking = true;
      if (window.requestAnimationFrame) {
        window.requestAnimationFrame(sync);
      } else {
        setTimeout(sync, 80);
      }
    }

    window.addEventListener('scroll', requestSync, { passive: true });
    window.addEventListener('resize', requestSync);
    sync();
  });

})();
