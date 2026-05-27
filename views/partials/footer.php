<?php $year = date('Y'); $brand = config('brand.alt_name', '+QUECLIMA'); ?>
<footer class="bg-dark text-white py-4">
  <div class="container text-center">
    <p class="mb-1">
      &copy; <?php echo e($year.' '.$brand); ?>. <?php echo e(t('footer.rights')); ?>
    </p>

    <p class="mb-1"><?php echo e(t('footer.tagline')); ?></p>
    <?php if (($GLOBALS['current_lang'] ?? 'es') === 'es'): ?>
      <p class="mb-1"><a class="text-white" href="/es/servicios/">Servicios</a> | <a class="text-white" href="/es/zonas/">Zonas</a> | <a class="text-white" href="/es/blog/">Gu&iacute;as</a></p>
    <?php else: ?>
      <p class="mb-1"><a class="text-white" href="<?php echo e(lang_url($GLOBALS['current_lang'] ?? 'es')); ?>"><?php echo e(t('nav.home', 'Inicio')); ?></a> | <a class="text-white" href="<?php echo e(lang_url($GLOBALS['current_lang'] ?? 'es').'#faq'); ?>"><?php echo e(t('nav.faq', 'FAQ')); ?></a></p>
    <?php endif; ?>
    <p class="mb-0"><?php echo e(t('footer.service')); ?></p>
  </div>
</footer>
