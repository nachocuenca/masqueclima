<?php
$items = require __DIR__ . '/../app/content/cases/'.$current_lang.'.php';
$page_meta = ['title' => t('nav.cases').' | '.t('brand'), 'description'=> t('cases.desc')];
?>
<section class="container section">
  <h1><?php echo e(t('nav.cases')); ?></h1>
  <div class="cards">
  <?php foreach ($items as $c): ?>
    <article class="card">
      <h2><a href="<?php echo e(page_url('cases', $c['slug'])); ?>"><?php echo e($c['title']); ?></a></h2>
      <p><?php echo e($c['summary']); ?></p>
      <p><a class="btn" href="<?php echo e(page_url('cases', $c['slug'])); ?>"><?php echo e(t('cta.view_case')); ?></a></p>
    </article>
  <?php endforeach; ?>
  </div>
</section>
<?php include __DIR__.'/partials/contact.php'; ?>
