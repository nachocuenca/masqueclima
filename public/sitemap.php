<?php
// sitemap.xml dinámico por idiomas
header('Content-Type: application/xml; charset=utf-8');

$config = $GLOBALS['config'] ?? [];
$base   = rtrim($config['app']['base_url'] ?? '', '/');
$langs  = $config['brand']['langs'] ?? ['es'];

$now = date('c');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
<?php foreach ($langs as $l): 
  $loc = $base . '/' . $l . '/';
?>
  <url>
    <loc><?= htmlspecialchars($loc, ENT_XML1) ?></loc>
    <lastmod><?= $now ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>1.0</priority>
<?php foreach ($langs as $alt): 
  $href = $base . '/' . $alt . '/'; ?>
    <xhtml:link rel="alternate" hreflang="<?= $alt ?>" href="<?= htmlspecialchars($href, ENT_XML1) ?>" />
<?php endforeach; ?>
  </url>
<?php endforeach; ?>
</urlset>
