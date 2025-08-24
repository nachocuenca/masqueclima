<?php
$lang       = $GLOBALS['current_lang'] ?? 'es';
$langs      = $GLOBALS['config']['brand']['langs'] ?? ['es'];
$lang_names = [
  'es' => 'Español', 'en' => 'English', 'de' => 'Deutsch', 'nl' => 'Nederlands', 'ru' => 'Русский',
];

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
<header>
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
            <li class="nav-item"><a class="nav-link" href="#inicio"><?php echo e(t('nav.home')); ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#metodo"><?php echo e(t('nav.method')); ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#nosotros"><?php echo e(t('nav.about')); ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#zona"><?php echo e(t('nav.coverage')); ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#faq"><?php echo e(t('nav.faq')); ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#contacto"><?php echo e(t('nav.contact')); ?></a></li>
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
