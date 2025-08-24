<?php
$guides = require __DIR__ . '/../app/content/guides/'.$current_lang.'.php';
$page_meta = ['title' => t('nav.guides').' | '.t('brand'), 'description'=> t('guides.desc')];
?>
<section class="container section">
  <h1><?php echo e(t('nav.guides')); ?></h1>
  <div class="cards">
  <?php foreach ($guides as $g): ?>
    <article class="card">
      <h2><a href="<?php echo e(page_url('guides', $g['slug'])); ?>"><?php echo e($g['title']); ?></a></h2>
      <?php if (!empty($g['content'][0])): ?><p><?php echo e($g['content'][0]); ?></p><?php endif; ?>
      <p><a class="btn" href="<?php echo e(page_url('guides', $g['slug'])); ?>"><?php echo e(t('cta.read')); ?></a></p>
    </article>
  <?php endforeach; ?>
  </div>
</section>
<?php include __DIR__.'/partials/contact.php'; ?>
