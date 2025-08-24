<?php
$lang = $GLOBALS['current_lang'] ?? 'es';
?>
<section class="contact" id="contacto">
  <div class="container">
    <h2 class="section-title"><?php echo e(t('contact.title')); ?></h2>
    <div class="contact-content">
      <div class="contact-info">
        <h3><?php echo e(t('contact.info_title')); ?></h3>
        <div class="contact-item"><i>&#128222;</i> <div><strong><?php echo e(t('contact.phone')); ?>:</strong><br><a href="tel:+34613026600">+34 613 02 66 00</a></div></div>
        <div class="contact-item"><i>&#128231;</i> <div><strong><?php echo e(t('contact.email')); ?>:</strong><br><a href="mailto:info@masqueclima.es">info@masqueclima.es</a></div></div>
        <div class="contact-item"><i>&#128205;</i> <div><strong><?php echo e(t('contact.area')); ?>:</strong><br><?php echo e(t('coverage.short') ?? 'Provincia de Alicante'); ?></div></div>
        <div class="contact-item"><i>&#128338;</i> <div><strong><?php echo e(t('contact.hours')); ?>:</strong><br>Lunes a Sábado: 8:00 - 20:00</div></div>
      </div>

      <div class="contact-form">
        <h3><?php echo e(t('contact.form_title')); ?></h3>
        <form method="post" novalidate class="js-track-form" data-ev="form_submit">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="lang" value="<?php echo e($lang); ?>">
          <input type="text" name="company" value="" style="display:none" tabindex="-1" autocomplete="off"> <!-- honeypot -->
          <div class="form-group">
            <label for="nombre"><?php echo e(t('form.name')); ?> *</label>
            <input type="text" id="nombre" name="name" required>
          </div>
          <div class="form-group">
            <label for="telefono"><?php echo e(t('form.phone')); ?> *</label>
            <input type="tel" id="telefono" name="phone" required>
          </div>
          <div class="form-group">
            <label for="email"><?php echo e(t('form.email')); ?></label>
            <input type="email" id="email" name="email">
          </div>
          <div class="form-group">
            <label for="servicio"><?php echo e(t('form.service')); ?></label>
            <select id="servicio" name="service" class="form-select">
              <option value=""><?php echo e(t('form.select')); ?></option>
              <option value="climatizacion"><?php echo e(t('service.install_ac')); ?></option>
              <option value="mantenimiento"><?php echo e(t('service.maintenance')); ?></option>
              <option value="calefaccion"><?php echo e(t('service.install_heating')); ?></option>
              <option value="electricidad"><?php echo e(t('service.electrical')); ?></option>
              <option value="fontaneria"><?php echo e(t('service.plumbing')); ?></option>
              <option value="urgente"><?php echo e(t('service.urgent')); ?></option>
            </select>
          </div>
          <div class="form-group">
            <label for="mensaje"><?php echo e(t('form.desc')); ?></label>
            <textarea id="mensaje" name="message" rows="4"></textarea>
          </div>
          <button type="submit" class="submit-btn js-track" data-ev="form_submit" name="contact_submit" value="1"><?php echo e(t('form.submit')); ?></button>
        </form>
      </div>
    </div>
  </div>
</section>
