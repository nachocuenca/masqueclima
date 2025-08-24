<?php $year = date('Y'); ?>
<footer class="bg-dark text-white py-4">
  <div class="container text-center">
    <p class="mb-1">
      &copy; <?php echo $year; ?> +QUECLIMA.
      <?php echo e(t('footer.rights', 'Todos los derechos reservados.')); ?>
    </p>

    <p class="mb-1">
      <?php
        // Soporta tus claves actuales (line2/line3) y también las nuevas (tagline/service) si algún día las usas.
        $tagline = t('footer.tagline', t('footer.line2', 'Especialistas en climatización, calefacción, fontanería y electricidad en la provincia de Alicante.'));
        echo e($tagline);
      ?>
    </p>

    <p class="mb-0">
      <?php
        $service = t('footer.service', t('footer.line3', 'Servicio técnico en Benidorm, Altea, La Nucía, Calpe, Finestrat, Alfaz del Pi y alrededores.'));
        echo e($service);
      ?>
    </p>
  </div>
</footer>
