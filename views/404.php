<?php /* views/404.php */ ?>
<section class="container py-5" id="not-found">
  <div class="row justify-content-center">
    <div class="col-lg-8 text-center">
      <h1 class="display-5 fw-bold mb-3">404</h1>
      <p class="lead mb-4">
        <?php echo e(t('404.message', ['{site}' => '+QUECLIMA'])); ?>
      </p>
      <a href="<?php echo e(lang_url($GLOBALS['current_lang'] ?? 'es')); ?>" class="btn btn-primary btn-lg">
        <?php echo e(t('404.back_home')); ?>
      </a>
    </div>
  </div>
</section>
