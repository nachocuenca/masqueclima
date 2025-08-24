<?php
$items = require __DIR__ . '/../app/content/cases/'.$current_lang.'.php';
$slug = $params['slug'] ?? '';
$item = null;
foreach ($items as $it) if ($it['slug'] === $slug) { $item = $it; break; }
if (!$item) { include __DIR__.'/404.php'; return; }
$page_meta = ['title'=>$item['title'].' | '.t('brand'), 'description'=>$item['summary']];
$breadcrumbs = [
  t('home') => lang_url($current_lang),
  t('nav.cases') => page_url('cases'),
  $item['title'] => page_url('cases', $item['slug']),
];
?>
<section class="container section">
  <h1><?php echo e($item['title']); ?></h1>
  <p class="lead"><?php echo e($item['summary']); ?></p>
  <ul>
    <?php foreach ($item['details'] as $d): ?><li><?php echo e($d); ?></li><?php endforeach; ?>
  </ul>
  <p><strong><?php echo e(t('cases.result')); ?>:</strong> <?php echo e($item['result']); ?></p>
</section>
<?php include __DIR__.'/partials/contact.php'; ?>
<?php print_breadcrumbs_jsonld($breadcrumbs); ?>
