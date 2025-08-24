<?php
$services = require __DIR__ . '/../app/content/services/'.$current_lang.'.php';
$page_meta = ['title' => t('nav.services').' | '.t('brand'), 'description'=> t('services.desc')];
?>
<section class="container section">
  <h1><?php echo e(t('nav.services')); ?></h1>
  <div class="cards">
  <?php foreach ($services as $s): ?>
    <article class="card">
      <h2><a href="<?php echo e(page_url('services', $s['slug'])); ?>"><?php echo e($s['title']); ?></a></h2>
      <p><?php echo e($s['summary']); ?></p>
      <p><a class="btn" href="<?php echo e(page_url('services', $s['slug'])); ?>"><?php echo e(t('cta.learn_more')); ?></a></p>
    </article>
  <?php endforeach; ?>
  </div>
</section>
<?php include __DIR__.'/partials/contact.php'; ?>
