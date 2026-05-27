<?php $year = date('Y'); $brand = config('brand.alt_name', '+QUECLIMA'); ?>
<footer class="bg-dark text-white py-4">
  <div class="container text-center">
    <p class="mb-1">
      &copy; <?php echo e($year.' '.$brand); ?>. <?php echo e(t('footer.rights')); ?>
    </p>

    <p class="mb-1"><?php echo e(t('footer.tagline')); ?></p>
    <?php if (($GLOBALS['current_lang'] ?? 'es') === 'es'): ?>
      <p class="mb-1"><a class="text-white" href="/es/servicios/">Servicios</a> | <a class="text-white" href="/es/zonas/">Zonas</a> | <a class="text-white" href="/es/blog/">Gu&iacute;as</a></p>
    <?php endif; ?>
    <p class="mb-0"><?php echo e(t('footer.service')); ?></p>
  </div>
</footer>
