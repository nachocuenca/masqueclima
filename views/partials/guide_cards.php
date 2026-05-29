<?php
// Dynamic guide cards: load first 3 guides for current language (fall back to 'es')
 $requestPath = $_SERVER['REQUEST_URI'] ?? '/es/';
 if (preg_match('#^/([a-z]{2})/#', $requestPath, $m)) { $lang = $m[1]; } else { $lang = 'es'; }
 $guidesFile = __DIR__ . '/../../app/content/guides/' . $lang . '.php';
 $cards = [];
 if (is_file($guidesFile)) {
   $all = require $guidesFile;
   $cards = array_slice($all, 0, 3);
 }
  $prefixMap = [
    'es' => '/es/blog/', 'en' => '/en/guides/', 'de' => '/de/ratgeber/',
    'nl' => '/nl/gidsen/', 'ru' => '/ru/gidy/', 'no' => '/no/guider/',
  ];
  $prefix = $prefixMap[$lang] ?? '/es/blog/';
  $guideLabelMap = [
    'es' => 'Guía', 'en' => 'Guide', 'de' => 'Ratgeber', 'nl' => 'Gids', 'ru' => 'Руководство', 'no' => 'Guide',
  ];
  $ctaLabelMap = [
    'es' => 'Leer guía', 'en' => 'Read guide', 'de' => 'Ratgeber lesen', 'nl' => 'Gids lezen', 'ru' => 'Читать руководство', 'no' => 'Les guide',
  ];
  $comingSoonMap = [
    'es' => 'Próximamente', 'en' => 'Coming soon', 'de' => 'Demnächst', 'nl' => 'Binnenkort', 'ru' => 'Скоро', 'no' => 'Kommer snart',
  ];
  $guideLabel = $guideLabelMap[$lang] ?? $guideLabelMap['es'];
  $ctaLabel = $ctaLabelMap[$lang] ?? $ctaLabelMap['es'];
  $comingSoon = $comingSoonMap[$lang] ?? $comingSoonMap['es'];
?>
<div class="hub-grid guides-grid">
  <?php foreach ($cards as $g): ?>
    <article class="hub-card">
      <span class="guide-badge guide-badge--live"><?php echo e($guideLabel); ?></span>
      <h3><a href="<?php echo e($prefix . $g['slug'] . '/'); ?>"><?php echo e($g['title']); ?></a></h3>
      <?php if (!empty($g['content'][0])): ?><p><?php echo e($g['content'][0]); ?></p><?php endif; ?>
      <a class="hub-pill" href="<?php echo e($prefix . $g['slug'] . '/'); ?>"><?php echo e($ctaLabel); ?></a>
    </article>
  <?php endforeach; ?>
  <article class="hub-card">
    <span class="guide-badge"><?php echo e($comingSoon); ?></span>
    <h3><?php echo e($lang === 'es' ? 'Aire acondicionado para apartamentos tur&iacute;sticos' : 'Air conditioning for holiday apartments'); ?></h3>
    <p><?php echo e($lang === 'es' ? 'Aspectos importantes para viviendas de alquiler vacacional: consumo, ruido, control y fiabilidad.' : 'Important factors for holiday rentals: consumption, noise, control and reliability.'); ?></p>
  </article>
  <article class="hub-card">
    <span class="guide-badge"><?php echo e($comingSoon); ?></span>
    <h3><?php echo e($lang === 'es' ? 'Split, multisplit o conductos: qu&eacute; sistema elegir' : 'Split, multi-split or ducted: which system to choose'); ?></h3>
    <p><?php echo e($lang === 'es' ? 'Diferencias principales entre sistemas y cu&aacute;ndo conviene cada soluci&oacute;n.' : 'Main differences between systems and when each solution makes sense.'); ?></p>
  </article>
</div>
<style>
  .guide-badge{display:inline-block;background:#e8f0fb;color:#0058b8;font-size:.78rem;font-weight:700;border-radius:999px;padding:.2rem .65rem;margin-bottom:.5rem;letter-spacing:.02em;}
  .guide-badge--live{background:#d1fae5;color:#065f46;}
  .hub-card h3 a{color:inherit;text-decoration:none;}
  .hub-card h3 a:hover{text-decoration:underline;}
</style>
