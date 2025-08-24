<?php
$items = require __DIR__ . '/../app/content/guides/'.$current_lang.'.php';
$slug = $params['slug'] ?? '';
$item = null;
foreach ($items as $it) if ($it['slug'] === $slug) { $item = $it; break; }
if (!$item) { include __DIR__.'/404.php'; return; }
$page_meta = ['title'=>$item['title'].' | '.t('brand'), 'description'=>$item['content'][0] ?? meta_description()];
$breadcrumbs = [
  t('home') => lang_url($current_lang),
  t('nav.guides') => page_url('guides'),
  $item['title'] => page_url('guides', $item['slug']),
];
$view_type = $item['type'] ?? 'guide';
if ($view_type === 'calculator') {
  include __DIR__ . '/guide_calculator.php';
} else {
  include __DIR__ . '/guide_simple.php';
}
print_breadcrumbs_jsonld($breadcrumbs);
