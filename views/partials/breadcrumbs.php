<?php
$lang     = $GLOBALS['current_lang'] ?? 'es';
$baseUrl  = lang_url($lang, '/'); // con barra final
$uriPath  = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');

// quitar prefijo idioma si existe
$parts    = array_values(array_filter(explode('/', $uriPath), 'strlen'));
if (!empty($parts) && in_array($parts[0], $GLOBALS['config']['brand']['langs'] ?? ['es'], true)) {
  array_shift($parts);
}

$trail = [];
$trail[] = ['name' => t('nav.home'), 'url' => lang_url($lang)];
$accum = '';
foreach ($parts as $p) {
  $accum .= '/'.$p;
  // etiqueta legible o traducción
  $labelMap = [
    'servicios' => t('crumb.services') ?? 'Servicios',
    'areas'     => t('crumb.areas')    ?? 'Zonas',
    'guias'     => t('crumb.guides')   ?? 'Guías',
    'casos'     => t('crumb.cases')    ?? 'Casos',
    'contacto'  => t('nav.contact')    ?? 'Contacto',
  ];
  $name = $labelMap[$p] ?? ucwords(str_replace(['-','_'],' ', $p));
  $trail[] = ['name' => $name, 'url' => rtrim($baseUrl, '/').$accum.'/'];
}
?>
<?php if (count($trail) > 1): ?>
<nav class="container" aria-label="breadcrumb">
  <ol class="breadcrumb my-3">
    <?php foreach ($trail as $i => $item): ?>
      <?php if ($i < count($trail)-1): ?>
        <li class="breadcrumb-item"><a href="<?php echo e($item['url']); ?>"><?php echo e($item['name']); ?></a></li>
      <?php else: ?>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e($item['name']); ?></li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ol>
</nav>

<!-- JSON-LD BreadcrumbList -->
<script type="application/ld+json">
<?php
$list = ['@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>[]];
foreach ($trail as $idx => $item) {
  $list['itemListElement'][] = [
    '@type' => 'ListItem',
    'position' => $idx+1,
    'name' => $item['name'],
    'item' => $item['url']
  ];
}
echo json_encode($list, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>
</script>
<?php endif; ?>
