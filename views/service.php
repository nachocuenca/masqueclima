<?php
$items = require __DIR__ . '/../app/content/services/'.$current_lang.'.php';
$slug = $params['slug'] ?? '';
$service = null;
foreach ($items as $it) if ($it['slug'] === $slug) { $service = $it; break; }
if (!$service) { include __DIR__.'/404.php'; return; }
$page_meta = ['title'=>$service['title'].' | '.t('brand'), 'description'=>$service['summary']];
$breadcrumbs = [
  t('home') => lang_url($current_lang),
  t('nav.services') => page_url('services'),
  $service['title'] => page_url('services', $service['slug']),
];
?>
<section class="container section">
  <h1><?php echo e($service['title']); ?></h1>
  <p class="lead"><?php echo e($service['summary']); ?></p>
  <?php foreach ($service['sections'] as $sec): ?>
    <h2><?php echo e($sec['h']); ?></h2>
    <p><?php echo e($sec['p']); ?></p>
  <?php endforeach; ?>

  <?php if (!empty($service['faq'])): ?>
  <h2><?php echo e(t('faq.title')); ?></h2>
  <?php foreach ($service['faq'] as $f): ?>
    <details><summary><?php echo e($f['q']); ?></summary><p><?php echo e($f['a']); ?></p></details>
  <?php endforeach; ?>
  <?php endif; ?>
</section>
<?php include __DIR__.'/partials/contact.php'; ?>
<?php print_service_jsonld($service); ?>
<?php print_breadcrumbs_jsonld($breadcrumbs); ?>
