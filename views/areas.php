<?php
$areas = require __DIR__ . '/../app/content/areas/'.$current_lang.'.php';
$page_meta = ['title' => t('nav.areas').' | '.t('brand'), 'description'=> t('areas.desc')];
?>
<section class="container section">
  <h1><?php echo e(t('nav.areas')); ?></h1>
  <ul class="grid-list">
    <?php foreach ($areas as $a): ?>
      <li><a href="<?php echo e(page_url('areas', $a['slug'])); ?>"><?php echo e($a['title']); ?></a></li>
    <?php endforeach; ?>
  </ul>
</section>
<?php include __DIR__.'/partials/contact.php'; ?>
