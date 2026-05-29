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
  $guideLabel = $guideLabelMap[$lang] ?? $guideLabelMap['es'];
  $ctaLabel = $ctaLabelMap[$lang] ?? $ctaLabelMap['es'];
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
</div>
<style>
  .guide-badge{display:inline-block;background:#e8f0fb;color:#0058b8;font-size:.78rem;font-weight:700;border-radius:999px;padding:.2rem .65rem;margin-bottom:.5rem;letter-spacing:.02em;}
  .guide-badge--live{background:#d1fae5;color:#065f46;}
  .hub-card h3 a{color:inherit;text-decoration:none;}
  .hub-card h3 a:hover{text-decoration:underline;}
</style>
