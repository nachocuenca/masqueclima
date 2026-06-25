<?php
$lang = $GLOBALS['current_lang'] ?? 'es';
$heatwaveFormNotices = [
  'es' => 'Alta demanda por ola de calor: las nuevas citas pueden tener una espera aproximada de un mes. Priorizamos incidencias urgentes de clientes actuales.',
  'en' => 'High demand due to the heatwave: new appointments may have an estimated wait of around one month. We prioritise urgent issues for existing customers.',
  'de' => 'Hohe Nachfrage wegen der Hitzewelle: Neue Termine k&ouml;nnen derzeit etwa einen Monat Wartezeit haben. Dringende Anliegen bestehender Kunden haben Vorrang.',
  'nl' => 'Hoge vraag door de hittegolf: voor nieuwe afspraken kan de wachttijd ongeveer een maand zijn. Spoedgevallen van bestaande klanten krijgen prioriteit.',
  'ru' => '&#1042;&#1099;&#1089;&#1086;&#1082;&#1080;&#1081; &#1089;&#1087;&#1088;&#1086;&#1089; &#1080;&#1079;-&#1079;&#1072; &#1078;&#1072;&#1088;&#1099;: &#1085;&#1086;&#1074;&#1099;&#1077; &#1074;&#1080;&#1079;&#1080;&#1090;&#1099; &#1084;&#1086;&#1075;&#1091;&#1090; &#1080;&#1084;&#1077;&#1090;&#1100; &#1086;&#1078;&#1080;&#1076;&#1072;&#1085;&#1080;&#1077; &#1086;&#1082;&#1086;&#1083;&#1086; &#1086;&#1076;&#1085;&#1086;&#1075;&#1086; &#1084;&#1077;&#1089;&#1103;&#1094;&#1072;. &#1057;&#1088;&#1086;&#1095;&#1085;&#1099;&#1077; &#1089;&#1083;&#1091;&#1095;&#1072;&#1080; &#1090;&#1077;&#1082;&#1091;&#1097;&#1080;&#1093; &#1082;&#1083;&#1080;&#1077;&#1085;&#1090;&#1086;&#1074; &#1074; &#1087;&#1088;&#1080;&#1086;&#1088;&#1080;&#1090;&#1077;.',
  'no' => 'Stor p&aring;gang p&aring; grunn av heteb&oslash;lgen: nye avtaler kan ha omtrent en m&aring;neds ventetid. Vi prioriterer akutte saker for eksisterende kunder.',
];
$heatwaveFormNotice = $heatwaveFormNotices[$lang] ?? $heatwaveFormNotices['es'];
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
        <div class="heatwave-form-notice" role="note" style="margin:0 0 1rem;padding:.75rem .9rem;border:1px solid #f3d18c;border-left:4px solid #d88916;border-radius:8px;background:#fff8e8;color:#4d3413;font-size:.92rem;line-height:1.45;">
          <span aria-hidden="true" style="display:inline-block;margin-right:.35rem;">&#9728;</span><?php echo $heatwaveFormNotice; ?>
        </div>
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
