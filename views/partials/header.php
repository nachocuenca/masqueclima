<?php
$lang       = $GLOBALS['current_lang'] ?? 'es';
$langs      = $GLOBALS['config']['brand']['langs'] ?? ['es'];
$lang_names = [
  'no' => 'Norsk',
  'es' => 'Español', 'en' => 'English', 'de' => 'Deutsch', 'nl' => 'Nederlands', 'ru' => 'Русский',
];

$heatwave_labels = [
  'es' => ['aria' => 'Aviso por alta demanda', 'banner' => 'Alta demanda por ola de calor: estamos recibiendo muchas solicitudes y podemos tardar m&aacute;s de lo habitual en responder. Las nuevas citas pueden tener una espera aproximada de un mes. Damos prioridad a incidencias urgentes de clientes actuales.', 'banner_mobile' => 'Ola de calor: alta demanda y respuesta m&aacute;s lenta. Nuevas citas: espera aprox. de un mes. Urgencias de clientes actuales, prioridad.'],
  'en' => ['aria' => 'High demand notice', 'banner' => 'High demand due to the heatwave: we are receiving many requests and may take longer than usual to respond. New appointments may have an estimated wait of around one month. We prioritise urgent issues for existing customers.', 'banner_mobile' => 'Heatwave: high demand and slower replies. New appointments: about a one-month wait. Urgent issues for existing customers take priority.'],
  'de' => ['aria' => 'Hinweis zu hoher Nachfrage', 'banner' => 'Hohe Nachfrage wegen der Hitzewelle: Wir erhalten viele Anfragen und die Antwort kann l&auml;nger als gewohnt dauern. Neue Termine k&ouml;nnen derzeit etwa einen Monat Wartezeit haben. Dringende Anliegen bestehender Kunden haben Vorrang.', 'banner_mobile' => 'Hitzewelle: hohe Nachfrage, Antworten dauern l&auml;nger. Neue Termine: ca. ein Monat Wartezeit. Notf&auml;lle bestehender Kunden haben Vorrang.'],
  'nl' => ['aria' => 'Melding hoge vraag', 'banner' => 'Hoge vraag door de hittegolf: we ontvangen veel aanvragen en antwoorden mogelijk later dan normaal. Voor nieuwe afspraken kan de wachttijd ongeveer een maand zijn. Spoedgevallen van bestaande klanten krijgen prioriteit.', 'banner_mobile' => 'Hittegolf: hoge vraag en tragere reacties. Nieuwe afspraken: ongeveer een maand wachttijd. Spoed voor bestaande klanten krijgt prioriteit.'],
  'ru' => ['aria' => '&#1059;&#1074;&#1077;&#1076;&#1086;&#1084;&#1083;&#1077;&#1085;&#1080;&#1077; &#1086; &#1074;&#1099;&#1089;&#1086;&#1082;&#1086;&#1084; &#1089;&#1087;&#1088;&#1086;&#1089;&#1077;', 'banner' => '&#1042;&#1099;&#1089;&#1086;&#1082;&#1080;&#1081; &#1089;&#1087;&#1088;&#1086;&#1089; &#1080;&#1079;-&#1079;&#1072; &#1078;&#1072;&#1088;&#1099;: &#1084;&#1099; &#1087;&#1086;&#1083;&#1091;&#1095;&#1072;&#1077;&#1084; &#1084;&#1085;&#1086;&#1075;&#1086; &#1079;&#1072;&#1087;&#1088;&#1086;&#1089;&#1086;&#1074; &#1080; &#1084;&#1086;&#1078;&#1077;&#1084; &#1086;&#1090;&#1074;&#1077;&#1095;&#1072;&#1090;&#1100; &#1076;&#1086;&#1083;&#1100;&#1096;&#1077; &#1086;&#1073;&#1099;&#1095;&#1085;&#1086;&#1075;&#1086;. &#1053;&#1086;&#1074;&#1099;&#1077; &#1074;&#1080;&#1079;&#1080;&#1090;&#1099; &#1084;&#1086;&#1075;&#1091;&#1090; &#1080;&#1084;&#1077;&#1090;&#1100; &#1086;&#1078;&#1080;&#1076;&#1072;&#1085;&#1080;&#1077; &#1086;&#1082;&#1086;&#1083;&#1086; &#1086;&#1076;&#1085;&#1086;&#1075;&#1086; &#1084;&#1077;&#1089;&#1103;&#1094;&#1072;. &#1057;&#1088;&#1086;&#1095;&#1085;&#1099;&#1077; &#1089;&#1083;&#1091;&#1095;&#1072;&#1080; &#1090;&#1077;&#1082;&#1091;&#1097;&#1080;&#1093; &#1082;&#1083;&#1080;&#1077;&#1085;&#1090;&#1086;&#1074; &#1074; &#1087;&#1088;&#1080;&#1086;&#1088;&#1080;&#1090;&#1077;.', 'banner_mobile' => '&#1046;&#1072;&#1088;&#1072;: &#1074;&#1099;&#1089;&#1086;&#1082;&#1080;&#1081; &#1089;&#1087;&#1088;&#1086;&#1089;, &#1086;&#1090;&#1074;&#1077;&#1095;&#1072;&#1077;&#1084; &#1076;&#1086;&#1083;&#1100;&#1096;&#1077;. &#1053;&#1086;&#1074;&#1099;&#1077; &#1074;&#1080;&#1079;&#1080;&#1090;&#1099;: &#1086;&#1078;&#1080;&#1076;&#1072;&#1085;&#1080;&#1077; &#1086;&#1082;&#1086;&#1083;&#1086; &#1084;&#1077;&#1089;&#1103;&#1094;&#1072;. &#1057;&#1088;&#1086;&#1095;&#1085;&#1099;&#1077; &#1089;&#1083;&#1091;&#1095;&#1072;&#1080; &#1090;&#1077;&#1082;&#1091;&#1097;&#1080;&#1093; &#1082;&#1083;&#1080;&#1077;&#1085;&#1090;&#1086;&#1074; &#1074; &#1087;&#1088;&#1080;&#1086;&#1088;&#1080;&#1090;&#1077;.'],
  'no' => ['aria' => 'Varsel om stor p&aring;gang', 'banner' => 'Stor p&aring;gang p&aring; grunn av heteb&oslash;lgen: vi mottar mange henvendelser og kan bruke lenger tid enn vanlig p&aring; &aring; svare. Nye avtaler kan ha omtrent en m&aring;neds ventetid. Vi prioriterer akutte saker for eksisterende kunder.', 'banner_mobile' => 'Heteb&oslash;lge: stor p&aring;gang og tregere svar. Nye avtaler: ca. &eacute;n m&aring;neds ventetid. Akutte saker for eksisterende kunder prioriteres.'],
];
$heatwave = $heatwave_labels[$lang] ?? $heatwave_labels['es'];

$render_flags = function($classes = '', $show_active_disabled = true) use ($langs, $lang, $lang_names) {
  ?>
  <ul class="navbar-nav lang-switch <?php echo $classes; ?>">
    <?php foreach ($langs as $l): ?>
      <?php
        $name = $lang_names[$l] ?? strtoupper($l);
        $isActive = ($l === $lang);
        $href = $isActive ? '#' : lang_switch_url($l);
        $cls  = 'nav-link lang-link';
        if ($isActive && $show_active_disabled) $cls .= ' active disabled';
      ?>
      <li class="nav-item">
        <a class="<?php echo $cls; ?>"
           href="<?php echo e($href); ?>"
           hreflang="<?php echo e($l); ?>"
           aria-label="<?php echo e($name); ?>"
           title="<?php echo e($name); ?>">
          <img src="<?php echo e(flag_url($l)); ?>" alt="<?php echo e($name); ?>" width="16" height="12" class="lang-flag" loading="lazy" decoding="async">
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php
};
?>
<header style="position:sticky;top:0;z-index:1040;width:100%;">
  <div class="heatwave-top-banner" role="note" aria-label="<?php echo e(html_entity_decode($heatwave['aria'], ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>" style="box-sizing:border-box;width:100%;background:#fff3d6;border-bottom:1px solid #efc56f;color:#4b3412;font-size:.9rem;line-height:1.28;">
    <div style="box-sizing:border-box;width:100%;max-width:1180px;margin:0 auto;padding:.42rem .95rem;display:flex;align-items:center;gap:.65rem;">
      <span class="heatwave-banner-icon" aria-hidden="true" style="flex:0 0 auto;display:inline-flex;align-items:center;justify-content:center;width:1.55rem;height:1.55rem;font-size:1.35rem;line-height:1;">&#9728;</span>
      <span class="heatwave-text-desktop" style="min-width:0;flex:1 1 auto;overflow-wrap:anywhere;"><?php echo $heatwave['banner']; ?></span>
      <span class="heatwave-text-mobile" style="min-width:0;flex:1 1 auto;overflow-wrap:anywhere;"><?php echo $heatwave['banner_mobile'] ?? $heatwave['banner']; ?></span>
    </div>
  </div>
  <style>html{scroll-padding-top:140px}body .hero{margin-top:0!important}header{position:sticky!important;top:0!important;z-index:1040!important;width:100%!important}.navbar.fixed-top{position:static!important;top:auto!important}.heatwave-top-banner{position:relative!important}.heatwave-text-mobile{display:none}.heatwave-form-notice{box-shadow:none!important}[id]{scroll-margin-top:140px}@media(max-width:767.98px){html{scroll-padding-top:150px}[id]{scroll-margin-top:150px}.heatwave-top-banner{font-size:.79rem!important;line-height:1.22!important}.heatwave-top-banner>div{padding:.32rem .72rem!important;gap:.5rem!important;align-items:center!important}.heatwave-banner-icon{width:1.65rem!important;height:1.65rem!important;font-size:1.45rem!important}.heatwave-text-desktop{display:none!important}.heatwave-text-mobile{display:inline!important}}</style>
  <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container d-flex align-items-center">

      <!-- Logo (izquierda) -->
      <a class="navbar-brand order-1" href="<?php echo e(lang_url($lang)); ?>">
        <img src="<?php echo e(asset('img/masqueclimalogo_.png')); ?>" alt="+QUECLIMA" width="180" height="60" style="height:60px;">
      </a>

      <!-- Menú centrado -->
      <div class="collapse navbar-collapse justify-content-center flex-grow-1 order-2" id="navbarNav">
        <div class="mobile-panel"><!-- estilizada sólo en móvil -->
          <ul class="navbar-nav main-menu mx-lg-auto">
            <?php foreach (primary_nav_items($lang) as $item): ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo e($item['href']); ?>"><?php echo e($item['label']); ?></a></li>
            <?php endforeach; ?>
          </ul>

          <!-- Banderas en móvil (en línea) -->
          <?php $render_flags('d-flex d-lg-none flex-row gap-1 mt-2'); ?>
        </div>
      </div>

      <!-- Toggler (móvil) -->
      <button class="navbar-toggler ms-auto order-3 d-lg-none"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#navbarNav"
              aria-controls="navbarNav"
              aria-expanded="false"
              aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Banderas en escritorio (derecha) -->
      <div class="order-4 ms-auto d-none d-lg-flex align-items-center">
        <?php $render_flags(); ?>
      </div>

    </div>
  </nav>
</header>
