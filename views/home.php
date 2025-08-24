<?php /* Home sin textos hardcodeados */ ?>
<?php
// Helper para imprimir traducciones SOLO si existen.
// Si una clave falta, devuelve cadena vacía (no muestra "clave.sinquedar").
$TT = function(string $key): string {
  $v = t($key);
  return $v === $key ? '' : e($v);
};
?>

<!-- =======================
     HERO
======================= -->
<section class="hero" id="inicio">
  <div class="hero-bg">
    <video class="hero-video" autoplay muted loop playsinline>
      <source src="<?= asset('img/0_Air_Conditioner_Remote_Control_3840x2160.mp4'); ?>" type="video/mp4">
    </video>
  </div>
  <div class="container">
    <h1><?php echo $TT('hero.title'); ?></h1>
    <p><?php echo $TT('hero.subtitle'); ?></p>
    <div class="cta-group">
      <a href="#contacto" class="cta-button js-track" data-ev="cta_quote"><?php echo $TT('cta.quote'); ?></a>
      <a href="https://wa.me/34613026600" class="cta-button cta-whatsapp js-track" data-ev="cta_whatsapp" target="_blank" rel="noopener">
        <?php echo $TT('cta.whatsapp'); ?>
      </a>
    </div>
  </div>
</section>

<!-- =======================
     MÉTODO / SERVICIOS
======================= -->
<section class="services" id="metodo">
  <div class="container">
    <h2 class="section-title"><?php echo $TT('method.title'); ?></h2>
    <div class="services-grid">
      <div class="service-card">
        <img src="<?= asset('img/Asesoramiento.png'); ?>" alt="<?php echo $TT('img.asesoramiento_alt'); ?>">
        <h3><?php echo $TT('method.s1.title'); ?></h3>
        <p><?php echo $TT('method.s1.text'); ?></p>
      </div>
      <div class="service-card">
        <img src="<?= asset('img/Venta.png'); ?>" alt="<?php echo $TT('img.seleccion_alt'); ?>">
        <h3><?php echo $TT('method.s2.title'); ?></h3>
        <p><?php echo $TT('method.s2.text'); ?></p>
      </div>
      <div class="service-card">
        <img src="<?= asset('img/Instalacion.png'); ?>" alt="<?php echo $TT('img.instalacion_alt'); ?>">
        <h3><?php echo $TT('method.s3.title'); ?></h3>
        <p><?php echo $TT('method.s3.text'); ?></p>
      </div>
      <div class="service-card">
        <img src="<?= asset('img/postVenta.png'); ?>" alt="<?php echo $TT('img.mantenimiento_alt'); ?>">
        <h3><?php echo $TT('method.s4.title'); ?></h3>
        <p><?php echo $TT('method.s4.text'); ?></p>
      </div>
    </div>
  </div>
</section>

<!-- =======================
     QUIÉNES SOMOS
======================= -->
<section class="nosotros" id="nosotros">
  <div class="container">
    <h2 class="nosotros-title"><?php echo $TT('about.title'); ?></h2>
    <div class="nosotros-desc">
      <?php if ($TT('about.p1')): ?><p><?php echo $TT('about.p1'); ?></p><?php endif; ?>
      <?php if ($TT('about.p2')): ?><p><?php echo $TT('about.p2'); ?></p><?php endif; ?>
      <?php if ($TT('about.p3')): ?><p><?php echo $TT('about.p3'); ?></p><?php endif; ?>
      <?php if ($TT('about.p4')): ?><p><?php echo $TT('about.p4'); ?></p><?php endif; ?>
      <?php if ($TT('about.cert')): ?><p><?php echo $TT('about.cert'); ?></p><?php endif; ?>
    </div>
  </div>
</section>

<!-- =======================
     COBERTURA / ZONA
======================= -->
<section class="zona" id="zona">
  <div class="container">
    <h2 class="zona-title"><?php echo $TT('coverage.title'); ?></h2>
    <div class="zona-lista">
      <strong><?php echo $TT('coverage.text'); ?></strong>
    </div>
    <div class="zona-mapa">
      <iframe
        title="<?php echo $TT('coverage.map_title'); ?>"
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12725.620917144712!2d-0.136398!3d38.538232!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd6221f6b0e9dffd%3A0x3ee4adcdf1ef9be4!2sBenidorm%2C%20Alicante!5e0!3m2!1ses!2ses!4v1689843948108!5m2!1ses!2ses"
        width="100%" height="320" style="border:0; border-radius:10px;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
  </div>
</section>

<!-- =======================
     FAQ (ACORDEÓN)
======================= -->
<section class="faq" id="faq">
  <div class="container">
    <h2 class="faq-title"><?php echo $TT('faq.title'); ?></h2>

    <div class="accordion" id="faqAccordion">

      <div class="accordion-item">
        <h2 class="accordion-header" id="h1">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#c1" aria-expanded="true" aria-controls="c1">
            <?php echo $TT('faq.q1'); ?>
          </button>
        </h2>
        <div id="c1" class="accordion-collapse collapse show" aria-labelledby="h1" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            <?php echo $TT('faq.a1'); ?>
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="h2">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c2" aria-expanded="false" aria-controls="c2">
            <?php echo $TT('faq.q2'); ?>
          </button>
        </h2>
        <div id="c2" class="accordion-collapse collapse" aria-labelledby="h2" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            <?php echo $TT('faq.a2'); ?>
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="h3">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c3" aria-expanded="false" aria-controls="c3">
            <?php echo $TT('faq.q3'); ?>
          </button>
        </h2>
        <div id="c3" class="accordion-collapse collapse" aria-labelledby="h3" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            <ul>
              <?php if ($TT('faq.a3.i1')): ?><li><?php echo $TT('faq.a3.i1'); ?></li><?php endif; ?>
              <?php if ($TT('faq.a3.i2')): ?><li><?php echo $TT('faq.a3.i2'); ?></li><?php endif; ?>
              <?php if ($TT('faq.a3.i3')): ?><li><?php echo $TT('faq.a3.i3'); ?></li><?php endif; ?>
              <?php if ($TT('faq.a3.i4')): ?><li><?php echo $TT('faq.a3.i4'); ?></li><?php endif; ?>
              <?php if ($TT('faq.a3.i5')): ?><li><?php echo $TT('faq.a3.i5'); ?></li><?php endif; ?>
            </ul>
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="h4">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c4" aria-expanded="false" aria-controls="c4">
            <?php echo $TT('faq.q4'); ?>
          </button>
        </h2>
        <div id="c4" class="accordion-collapse collapse" aria-labelledby="h4" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            <?php echo $TT('faq.a4'); ?>
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="h5">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c5" aria-expanded="false" aria-controls="c5">
            <?php echo $TT('faq.q5'); ?>
          </button>
        </h2>
        <div id="c5" class="accordion-collapse collapse" aria-labelledby="h5" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            <?php echo $TT('faq.a5'); ?>
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="h6">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c6" aria-expanded="false" aria-controls="c6">
            <?php echo $TT('faq.q6'); ?>
          </button>
        </h2>
        <div id="c6" class="accordion-collapse collapse" aria-labelledby="h6" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            <?php echo $TT('faq.a6'); ?>
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="h7">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c7" aria-expanded="false" aria-controls="c7">
            <?php echo $TT('faq.q7'); ?>
          </button>
        </h2>
        <div id="c7" class="accordion-collapse collapse" aria-labelledby="h7" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            <?php echo $TT('faq.a7'); ?>
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="h8">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c8" aria-expanded="false" aria-controls="c8">
            <?php echo $TT('faq.q8'); ?>
          </button>
        </h2>
        <div id="c8" class="accordion-collapse collapse" aria-labelledby="h8" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            <?php echo $TT('faq.a8'); ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- =======================
     MARCAS (Swiper)
======================= -->
<section class="marcas py-5 bg-light">
  <div class="container">
    <h2 class="section-title text-center mb-4"><?php echo $TT('brands.title'); ?></h2>
    <div class="swiper brand-swiper">
      <div class="swiper-wrapper align-items-center">
        <div class="swiper-slide"><img src="<?= asset('img/Daikin-Logo.png'); ?>" alt="<?php echo $TT('brands.daikin_alt'); ?>" class="brand-logo"></div>
        <div class="swiper-slide"><img src="<?= asset('img/Mitsubishi-Electric-Logo.png'); ?>" alt="<?php echo $TT('brands.mitsubishi_alt'); ?>" class="brand-logo"></div>
        <div class="swiper-slide"><img src="<?= asset('img/Logo-Fujitsu.png'); ?>" alt="<?php echo $TT('brands.fujitsu_alt'); ?>" class="brand-logo"></div>
        <div class="swiper-slide"><img src="<?= asset('img/Logo Panasonic.png'); ?>" alt="<?php echo $TT('brands.panasonic_alt'); ?>" class="brand-logo"></div>
        <div class="swiper-slide"><img src="<?= asset('img/Haier-Logo.wine.png'); ?>" alt="<?php echo $TT('brands.haier_alt'); ?>" class="brand-logo"></div>
        <div class="swiper-slide"><img src="<?= asset('img/LG-logo.png'); ?>" alt="<?php echo $TT('brands.lg_alt'); ?>" class="brand-logo"></div>
        <div class="swiper-slide"><img src="<?= asset('img/gree-logo.png'); ?>" alt="<?php echo $TT('brands.gree_alt'); ?>" class="brand-logo"></div>
        <div class="swiper-slide"><img src="<?= asset('img/giatsu-01.png'); ?>" alt="<?php echo $TT('brands.giatsu_alt'); ?>" class="brand-logo"></div>
      </div>
    </div>
  </div>
</section>

<!-- =======================
     CONTACTO (con CSRF)
======================= -->
<section class="contact" id="contacto">
  <div class="container">
    <h2 class="section-title"><?php echo $TT('contact.title'); ?></h2>

    <?php if ($msg = flash('form_ok')): ?>
      <div class="alert alert-success" role="alert" style="max-width:950px;margin:0 auto 1rem;">
        <?php echo e($msg); ?>
      </div>
    <?php endif; ?>

    <div class="contact-content">
      <div class="contact-info">
        <h3><?php echo $TT('contact.info_title'); ?></h3>
        <div class="contact-item"><i>&#128222;</i>
          <div>
            <strong><?php echo $TT('contact.phone'); ?>:</strong><br>+34 613 02 66 00
          </div>
        </div>
        <div class="contact-item"><i>&#128231;</i>
          <div>
            <strong><?php echo $TT('contact.email'); ?>:</strong><br>info@masqueclima.es
          </div>
        </div>
        <div class="contact-item"><i>&#128205;</i>
          <div>
            <?php if ($TT('contact.area')): ?><strong><?php echo $TT('contact.area'); ?>:</strong><br><?php endif; ?>
            <?php echo $TT('contact.area_text'); ?>
          </div>
        </div>
        <div class="contact-item"><i>&#128338;</i>
          <div>
            <?php if ($TT('contact.hours_label')): ?><strong><?php echo $TT('contact.hours_label'); ?>:</strong><br><?php endif; ?>
            <?php echo $TT('contact.hours_text'); ?>
          </div>
        </div>
      </div>

      <div class="contact-form">
        <h3><?php echo $TT('contact.form_title'); ?></h3>
        <form method="post" novalidate>
          <input type="hidden" name="company" value="">
          <div class="form-group">
            <label for="nombre"><?php echo $TT('form.name'); ?> *</label>
            <input type="text" id="nombre" name="name" required>
          </div>
          <div class="form-group">
            <label for="telefono"><?php echo $TT('form.phone'); ?> *</label>
            <input type="tel" id="telefono" name="phone" required>
          </div>
          <div class="form-group">
            <label for="email"><?php echo $TT('form.email'); ?></label>
            <input type="email" id="email" name="email">
          </div>
          <div class="form-group">
            <label for="servicio"><?php echo $TT('form.service'); ?></label>
            <select id="servicio" name="service" class="form-select">
              <option value=""><?php echo $TT('form.select'); ?></option>
              <option value="climatizacion"><?php echo $TT('service.install_ac'); ?></option>
              <option value="mantenimiento"><?php echo $TT('service.maintenance'); ?></option>
              <option value="calefaccion"><?php echo $TT('service.install_heating'); ?></option>
              <option value="electricidad"><?php echo $TT('service.electrical'); ?></option>
              <option value="fontaneria"><?php echo $TT('service.plumbing'); ?></option>
              <option value="urgente"><?php echo $TT('service.urgent'); ?></option>
            </select>
          </div>
          <div class="form-group">
            <label for="mensaje"><?php echo $TT('form.desc'); ?></label>
            <textarea id="mensaje" name="message" rows="4"></textarea>
          </div>
          <button type="submit" class="submit-btn js-track" data-ev="form_submit" name="contact_submit" value="1">
            <?php echo $TT('form.submit'); ?>
          </button>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- =======================
     OPINIONES (ELFSIGHT)
======================= -->
<section class="opiniones" id="opiniones" style="background:#fff;">
  <div class="container">
    <h2 class="text-center mb-3"><?php echo $TT('reviews.title'); ?></h2>
    <?php if ($TT('reviews.desc') || $TT('reviews.helper')): ?>
      <p class="text-center mb-4">
        <?php if ($TT('reviews.desc')): ?><?php echo $TT('reviews.desc'); ?><?php endif; ?>
        <?php if ($TT('reviews.helper')): ?> <?php echo $TT('reviews.helper'); ?><?php endif; ?>
      </p>
    <?php endif; ?>
    <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
    <div class="elfsight-app-49da578d-ec47-4904-b0c4-91404d9d1b7e" data-elfsight-app-lazy></div>
    <div class="text-center my-4">
      <a href="#contacto" class="btn btn-primary btn-lg js-track" data-ev="reviews_cta"><?php echo $TT('reviews.cta'); ?></a>
    </div>
  </div>
</section>

<!-- =======================
     WHATSAPP FLOAT
======================= -->
<a href="https://wa.me/34613026600" class="whatsapp-floating js-track" data-ev="whatsapp_float" target="_blank" rel="noopener" aria-label="<?php echo $TT('cta.whatsapp'); ?>">
  <span><?php echo $TT('cta.whatsapp'); ?></span>
  <img src="<?= asset('img/WhatsApp.png'); ?>" alt="<?php echo $TT('img.whatsapp_alt'); ?>" class="WhatsApp" style="width:20px;height:20px;">
</a>
