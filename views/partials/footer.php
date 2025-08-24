<?php $year = date('Y'); $brand = config('brand.alt_name', '+QUECLIMA'); ?>
<footer class="bg-dark text-white py-4">
  <div class="container text-center">
    <p class="mb-1">
      &copy; <?php echo e($year.' '.$brand); ?>. <?php echo e(t('footer.rights')); ?>
    </p>

    <p class="mb-1"><?php echo e(t('footer.tagline')); ?></p>
    <p class="mb-0"><?php echo e(t('footer.service')); ?></p>
  </div>
</footer>
