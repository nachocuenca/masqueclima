<?php
?>
<section class="container section">
  <h1><?php echo e($item['title']); ?></h1>
  <?php foreach (($item['content'] ?? []) as $p): ?>
    <p><?php echo e($p); ?></p>
  <?php endforeach; ?>
  <?php if (!empty($item['faq'])): ?>
  <h2><?php echo e(t('faq.title')); ?></h2>
  <?php foreach ($item['faq'] as $f): ?>
    <details><summary><?php echo e($f['q']); ?></summary><p><?php echo e($f['a']); ?></p></details>
  <?php endforeach; endif; ?>
</section>
<?php include __DIR__.'/partials/contact.php'; ?>
