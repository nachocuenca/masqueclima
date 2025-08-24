<?php
$items = require __DIR__ . '/../app/content/areas/'.$current_lang.'.php';
$slug = $params['slug'] ?? '';
$area = null;
foreach ($items as $it) if ($it['slug'] === $slug) { $area = $it; break; }
if (!$area) { include __DIR__.'/404.php'; return; }
$page_meta = ['title'=>$area['title'].' | '.t('brand'), 'description'=>$area['intro']];
$breadcrumbs = [
  t('home') => lang_url($current_lang),
  t('nav.areas') => page_url('areas'),
  $area['title'] => page_url('areas', $area['slug']),
];
?>
<section class="container section">
  <h1><?php echo e($area['title']); ?></h1>
  <p class="lead"><?php echo e($area['intro']); ?></p>
  <ul>
    <?php foreach ($area['points'] as $p): ?><li><?php echo e($p); ?></li><?php endforeach; ?>
  </ul>
</section>
<?php include __DIR__.'/partials/contact.php'; ?>
<?php print_breadcrumbs_jsonld($breadcrumbs); ?>
