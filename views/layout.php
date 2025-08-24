<?php
// expone el nombre de vista para meta dinámico
$GLOBALS['__view_name__'] = $view ?? 'home';
?><!DOCTYPE html>
<html lang="<?php echo e($GLOBALS['current_lang'] ?? 'es'); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?php echo e(meta_title()); ?></title>
  <meta name="description" content="<?php echo e(meta_description()); ?>">
  <link rel="canonical" href="<?php echo e(lang_url($GLOBALS['current_lang'] ?? 'es')); ?>">
  <?php print_hreflang(); ?>

  <!-- OG/Twitter -->
  <?php print_og_locales(); ?>
  <meta property="og:title" content="<?php echo e(meta_title()); ?>">
  <meta property="og:description" content="<?php echo e(meta_description()); ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo e(lang_url($GLOBALS['current_lang'] ?? 'es')); ?>">
  <meta property="og:image" content="<?php echo e(base_url().'/img/masqueclimalogo_.png'); ?>">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo e(meta_title()); ?>">
  <meta name="twitter:description" content="<?php echo e(meta_description()); ?>">
  <meta name="twitter:image" content="<?php echo e(base_url().'/img/masqueclimalogo_.png'); ?>">
<meta property="og:image" content="<?php echo asset('assets/img/og.jpg'); ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo e(meta_title()); ?>">
<meta name="twitter:description" content="<?php echo e(meta_description()); ?>">
  <!-- Performance -->
  <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <!-- Fonts + CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo asset('assets/css/styles.css'); ?>">

  <!-- JSON-LD -->
  <?php print_jsonld(); ?>
</head>
<body>
  <?php include __DIR__.'/partials/header.php'; ?>

  <main id="main-content">
    <?php
      $view_file = __DIR__ . '/' . $view . '.php';
      if (!file_exists($view_file)) $view_file = __DIR__ . '/404.php';
      include $view_file;
    ?>
  </main>

  <?php include __DIR__.'/partials/footer.php'; ?>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
  <script src="<?php echo asset('assets/js/main.js'); ?>"></script>
</body>
</html>
