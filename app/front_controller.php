<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/helpers.php';

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (isset($_GET['__health'])) {
  header('Content-Type: text/plain; charset=UTF-8');
  echo 'OK';
  exit;
}

if ($uriPath === '/' && in_array($method, ['GET', 'HEAD'], true)) {
  $target = '/es/';
  $query = $_SERVER['QUERY_STRING'] ?? '';
  if ($query !== '') {
    $target .= '?' . $query;
  }
  header('Location: ' . $target, true, 301);
  exit;
}

if (!in_array($method, ['GET', 'HEAD'], true)) {
  http_response_code(405);
  header('Allow: GET, HEAD');
  exit;
}

$path = normalize_snapshot_path($uriPath);
$lang = detect_path_lang($path);
if ($lang !== null) {
  $GLOBALS['current_lang'] = $lang;
}

$hubHtml = render_es_hub_page($path);
if ($hubHtml !== null) {
  $html = patch_snapshot_html($hubHtml, $path, 'es');
  header('Content-Type: text/html; charset=UTF-8');
  echo $html;
  exit;
}

if ($lang !== null && $lang !== 'es') {
  $hubHtml = render_lang_hub_page($path, $lang);
  if ($hubHtml !== null) {
    $html = patch_snapshot_html($hubHtml, $path, $lang);
    header('Content-Type: text/html; charset=UTF-8');
    echo $html;
    exit;
  }
}

$snapshot = snapshot_file_for_path($path);
if ($snapshot === null) {
  http_response_code(404);
  $GLOBALS['view'] = '404';
  include __DIR__ . '/../views/layout.php';
  exit;
}

$html = file_get_contents($snapshot);
if ($html === false) {
  http_response_code(500);
  header('Content-Type: text/plain; charset=UTF-8');
  echo 'Snapshot read error';
  exit;
}

$html = patch_snapshot_html($html, $path, $lang ?? config('brand.default_lang', 'es'));

header('Content-Type: text/html; charset=UTF-8');
echo $html;

function normalize_snapshot_path(string $path): string {
  $path = '/' . ltrim($path, '/');
  if ($path !== '/' && !str_contains(basename($path), '.') && !str_ends_with($path, '/')) {
    $path .= '/';
  }
  return $path;
}

function snapshot_name_for_path(string $path): string {
  $trimmed = trim($path, '/');
  if ($trimmed === '') {
    return 'root.html';
  }
  return preg_replace('/[^A-Za-z0-9._-]+/', '__', $trimmed) . '.html';
}

function snapshot_file_for_path(string $path): ?string {
  $file = __DIR__ . '/snapshots/' . snapshot_name_for_path($path);
  return is_file($file) ? $file : null;
}

function detect_path_lang(string $path): ?string {
  $segments = array_values(array_filter(explode('/', trim($path, '/'))));
  $first = $segments[0] ?? null;
  $langs = config('brand.langs', ['es']);
  return is_string($first) && in_array($first, $langs, true) ? $first : null;
}

function patch_snapshot_html(string $html, string $path, string $lang): string {
  $home = '/' . rawurlencode($lang) . '/';
  $returnAnchor = str_contains($html, 'id="reformas-inicio"') ? '#reformas-inicio' : '#inicio';
  $returnTo = $path . $returnAnchor;

  $html = str_replace(
    'hreflang="x-default" href="https://masqueclima.es/"',
    'hreflang="x-default" href="https://masqueclima.es/es/"',
    $html
  );

  $html = preg_replace(
    '/(<a class="navbar-brand[^"]*" href=")https:\/\/masqueclima\.es\/("[^>]*>)/',
    '$1' . $home . '$2',
    $html
  ) ?? $html;

  $html = preg_replace(
    '/var HOME = "https:\/\/masqueclima\.es\/[^"]*";/',
    'var HOME = "' . addcslashes($home, '"\\') . '";',
    $html
  ) ?? $html;

  $html = preg_replace(
    '/<input type="hidden" name="csrf" value="[^"]*">/',
    '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">',
    $html
  ) ?? $html;

  $html = preg_replace(
    '/<input type="hidden" name="return_to" value="[^"]*">/',
    '<input type="hidden" name="return_to" value="' . e($returnTo) . '">',
    $html
  ) ?? $html;

  if (strtolower((string) config('app.env', 'production')) !== 'production') {
    $html = preg_replace(
      '/<head>/i',
      "<head>\n  <meta name=\"robots\" content=\"noindex, nofollow, noarchive\">",
      $html,
      1
    ) ?? $html;
  }

  if (!str_contains($html, 'application/ld+json')) {
    $jsonLd = snapshot_jsonld($path, $lang);
    $html = str_replace('</head>', $jsonLd . "\n</head>", $html);
  }

  $html = patch_snapshot_primary_nav($html, $lang);
  $html = patch_snapshot_remove_city_navigation_extras($html);
  $html = patch_snapshot_contact_anchor($html);
  $html = patch_snapshot_footer_guides_link($html, $lang);
  $html = patch_snapshot_home_context_links($html, $path, $lang);

  $html = patch_es_p1_location_page($html, $path, $lang);
  $html = patch_locality_hero_image($html, $path, $lang);

  $html = patch_snapshot_home_hero($html, $path, $lang);

  $statusScript = snapshot_feedback_script();
  if ($statusScript !== '') {
    $html = str_replace('</body>', $statusScript . "\n</body>", $html);
  }

  $parts = explode('</head>', $html, 2);
  if (count($parts) === 2) {
    $parts[1] = patch_snapshot_body_links($parts[1], $lang);
    $html = $parts[0] . '</head>' . $parts[1];
  }

  return $html;
}

function patch_snapshot_home_hero(string $html, string $path, string $lang): string {
  if (!snapshot_is_home_path($path, $lang) || str_contains($html, 'data-ev="cta_whatsapp_hero"')) {
    return $html;
  }

  $label = e(snapshot_whatsapp_label($lang));
  $button = "\n      <a class=\"cta-button cta-whatsapp js-track\" data-ev=\"cta_whatsapp_hero\" href=\"https://wa.me/34613026600\" target=\"_blank\" rel=\"noopener\" aria-label=\"{$label}\">\n        {$label}      </a>";

  return preg_replace_callback(
    '/(<section class="hero" id="inicio"[\s\S]*?<div class="cta-group">[\s\S]*?<a class="cta-button js-track"[\s\S]*?<\/a>)(\s*<\/div>)/',
    static function (array $matches) use ($button): string {
      return $matches[1] . $button . $matches[2];
    },
    $html,
    1
  ) ?? $html;
}

function snapshot_is_home_path(string $path, string $lang): bool {
  return $path === '/' . rawurlencode($lang) . '/';
}

function snapshot_whatsapp_label(string $lang): string {
  $fallbacks = [
    'es' => 'Escríbenos por WhatsApp',
    'en' => 'Message us on WhatsApp',
    'de' => 'WhatsApp-Nachricht senden',
    'nl' => 'Bericht via WhatsApp',
    'ru' => 'Написать в WhatsApp',
    'no' => 'Skriv til oss på WhatsApp',
  ];
  $fallback = $fallbacks[$lang] ?? $fallbacks['es'];
  $label = function_exists('t') ? t('cta.whatsapp', $fallback) : $fallback;

  return is_string($label) && $label !== '' && $label !== 'cta.whatsapp' ? $label : $fallback;
}

function patch_snapshot_primary_nav(string $html, string $lang): string {
  $items = primary_nav_items($lang);
  $nav = '';
  foreach ($items as $item) {
    $nav .= '            <li class="nav-item"><a class="nav-link" href="' .
      e((string) $item['href']) .
      '">' .
      e((string) $item['label']) .
      '</a></li>' .
      "\n";
  }

  return preg_replace(
    '/(<ul class="navbar-nav main-menu mx-lg-auto">\s*)[\s\S]*?(\s*<\/ul>)/',
    '$1' . "\n" . rtrim($nav) . "\n          " . '$2',
    $html,
    1
  ) ?? $html;
}

function patch_snapshot_remove_city_navigation_extras(string $html): string {
  return preg_replace(
    '/\s*<!-- Offcanvas de ciudades[\s\S]*?<\/script>\s*(?=<main id="main-content">)/',
    "\n",
    $html,
    1
  ) ?? $html;
}

function patch_snapshot_contact_anchor(string $html): string {
  if (str_contains($html, 'id="contacto"') || !str_contains($html, 'id="presupuesto"')) {
    return $html;
  }

  return preg_replace(
    '/(<section\b[^>]*\bid="presupuesto"[^>]*>)/i',
    '<span id="contacto" class="visually-hidden"></span>' . "\n" . '$1',
    $html,
    1
  ) ?? $html;
}

function patch_snapshot_footer_guides_link(string $html, string $lang): string {
  if (str_contains($html, 'footer-main-links')) {
    return $html;
  }

  $links = footer_main_links_html($lang);

  return preg_replace(
    '/(<footer class="bg-dark text-white py-4">[\s\S]*?<div class="container text-center">)/',
    '$1' . "\n" . '    ' . $links,
    $html,
    1
  ) ?? $html;
}

function footer_main_links_html(string $lang): string {
  if ($lang === 'es') {
    return '<p class="mb-1 footer-main-links"><a class="text-white" href="/es/servicios/">Servicios</a> | <a class="text-white" href="/es/zonas/">Zonas</a> | <a class="text-white" href="/es/blog/">Gu&iacute;as</a></p>';
  }

  $home = e(lang_url($lang));
  $faq = e(lang_url($lang) . '#faq');
  $homeLabel = e(t('nav.home', 'Home'));
  $faqLabel = e(t('nav.faq', 'FAQ'));

  $links = '<a class="text-white" href="' . $home . '">' . $homeLabel . '</a>';

  $servicesUrl = localized_hub_url($lang, 'services');
  $zonesUrl    = localized_hub_url($lang, 'zones');
  $guidesUrl   = localized_hub_url($lang, 'guides');

  if ($servicesUrl !== null) {
    $links .= ' | <a class="text-white" href="' . e($servicesUrl) . '">' . e(t('nav.services', 'Services')) . '</a>';
  }
  if ($zonesUrl !== null) {
    $links .= ' | <a class="text-white" href="' . e($zonesUrl) . '">' . e(t('nav.zones', 'Areas')) . '</a>';
  }
  if ($guidesUrl !== null) {
    $links .= ' | <a class="text-white" href="' . e($guidesUrl) . '">' . e(t('nav.guides', 'Guides')) . '</a>';
  }
  $links .= ' | <a class="text-white" href="' . $faq . '">' . $faqLabel . '</a>';

  return '<p class="mb-1 footer-main-links">' . $links . '</p>';
}

function patch_snapshot_home_context_links(string $html, string $path, string $lang): string {
  if ($lang !== 'es' || !snapshot_is_home_path($path, $lang) || str_contains($html, 'home-context-links')) {
    return $html;
  }

  $servicesLink = '    <p class="mt-4 mb-0 text-center home-context-links"><a class="btn btn-outline-primary" href="/es/servicios/">Ver servicios de climatizaci&oacute;n</a></p>' . "\n";
  $html = preg_replace_callback(
    '/(<section class="services-plain" id="metodo">[\s\S]*?)(\s*<\/div>\s*<\/section>)/',
    static function (array $matches) use ($servicesLink): string {
      return rtrim($matches[1]) . "\n" . $servicesLink . $matches[2];
    },
    $html,
    1
  ) ?? $html;

  $zonesLink = '    <p class="mt-3 mb-4 text-center home-context-links"><a class="btn btn-outline-primary" href="/es/zonas/">Ver zonas de servicio</a></p>' . "\n";
  $zonesNeedle = '    <div class="zona-mapa">';
  if (str_contains($html, $zonesNeedle)) {
    $html = str_replace($zonesNeedle, $zonesLink . $zonesNeedle, $html);
  }

  $guidesLink = '    <p class="text-center mb-4 home-context-links"><a class="btn btn-outline-primary" href="/es/blog/">Ver gu&iacute;as de climatizaci&oacute;n</a></p>' . "\n";
  $faqNeedle = '    <div class="accordion" id="faqAccordion">';
  if (str_contains($html, $faqNeedle)) {
    $html = str_replace($faqNeedle, $guidesLink . $faqNeedle, $html);
  }

  return $html;
}

function patch_snapshot_body_links(string $body, string $lang): string {
  return preg_replace_callback(
    '/\bhref=(["\'])(.*?)\1/i',
    static function (array $m) use ($lang): string {
      return 'href=' . $m[1] . rewrite_visible_href($m[2], $lang) . $m[1];
    },
    $body
  ) ?? $body;
}

function rewrite_visible_href(string $href, string $lang): string {
  $langs = config('brand.langs', ['es']);
  $defaultLang = (string) config('brand.default_lang', 'es');

  if (preg_match('~^https?://masqueclima\.es(/[^?#]*)?(\?[^#]*)?(#.*)?$~i', $href, $m)) {
    $path = $m[1] ?? '/';
    $query = $m[2] ?? '';
    $fragment = $m[3] ?? '';

    $setLang = query_lang($query, $langs);
    if ($setLang !== null) {
      return '/' . rawurlencode($setLang) . '/' . $fragment;
    }

    if ($path === '' || $path === '/') {
      $targetLang = $fragment !== '' ? $lang : $defaultLang;
      return '/' . rawurlencode($targetLang) . '/' . $fragment;
    }

    return $path . $fragment;
  }

  if (preg_match('~^\?setlang=([a-z]{2})(#.*)?$~i', $href, $m) && in_array($m[1], $langs, true)) {
    return '/' . rawurlencode($m[1]) . '/' . ($m[2] ?? '');
  }

  if (preg_match('~^/\?setlang=([a-z]{2})(#.*)?$~i', $href, $m) && in_array($m[1], $langs, true)) {
    return '/' . rawurlencode($m[1]) . '/' . ($m[2] ?? '');
  }

  if (str_starts_with($href, '/#')) {
    return '/' . rawurlencode($lang) . '/' . substr($href, 1);
  }

  if ($href === '/') {
    return '/' . rawurlencode($defaultLang) . '/';
  }

  return $href;
}

function query_lang(string $query, array $langs): ?string {
  if ($query === '') {
    return null;
  }
  parse_str(ltrim($query, '?'), $params);
  $lang = isset($params['setlang']) ? strtolower((string) $params['setlang']) : '';
  return in_array($lang, $langs, true) ? $lang : null;
}

function snapshot_jsonld(string $path, string $lang): string {
  $base = rtrim((string) config('brand.domain', 'https://masqueclima.es'), '/');
  $url = $base . $path;
  $brand = (string) config('brand.name', '+QUECLIMA');
  $logo = $base . '/assets/img/masqueclimalogo_.png';
  $phone = (string) config('brand.phone', '+34 613 02 66 00');
  $langs = config('brand.langs', ['es']);

  // Build BreadcrumbList based on path context
  $breadcrumbItems = [
    [
      '@type' => 'ListItem',
      'position' => 1,
      'name' => 'Inicio',
      'item' => $base . '/' . $lang . '/',
    ],
  ];

  if (preg_match('~^/es/aire-acondicionado-([a-z0-9-]+)/$~', $path, $m)) {
    $citySlug = $m[1];
    // Derive a readable city name from slug
    $cityName = ucwords(str_replace('-', ' ', $citySlug));
    $breadcrumbItems[] = [
      '@type' => 'ListItem',
      'position' => 2,
      'name' => 'Aire acondicionado en ' . $cityName,
      'item' => $url,
    ];
  } else {
    $localityPatterns = [
      'en' => ['~^/en/air-conditioning-([a-z0-9-]+)/$~', 'Air conditioning in %s'],
      'de' => ['~^/de/klimaanlage-([a-z0-9-]+)/$~', 'Klimaanlage in %s'],
      'nl' => ['~^/nl/airco-([a-z0-9-]+)/$~', 'Airco in %s'],
      'ru' => ['~^/ru/konditsioner-([a-z0-9-]+)/$~', 'Кондиционер в %s'],
      'no' => ['~^/no/aircondition-([a-z0-9-]+)/$~', 'Aircondition i %s'],
    ];
    $locPattern = $localityPatterns[$lang] ?? null;
    if ($locPattern !== null && preg_match($locPattern[0], $path, $m)) {
      $citySlug = $m[1];
      $cityName = ucwords(str_replace('-', ' ', $citySlug));
      $breadcrumbItems[] = [
        '@type' => 'ListItem',
        'position' => 2,
        'name' => sprintf($locPattern[1], $cityName),
        'item' => $url,
      ];
    }
  }

  $items = [
    [
      '@context' => 'https://schema.org',
      '@type' => 'Organization',
      'name' => $brand,
      'url' => $base . '/es/',
      'logo' => $logo,
    ],
    [
      '@context' => 'https://schema.org',
      '@type' => 'HVACBusiness',
      'name' => $brand,
      'url' => $url,
      'logo' => $logo,
      'telephone' => $phone,
      'areaServed' => config('brand.area'),
      'availableLanguage' => $langs,
      'serviceType' => ['Air conditioning installation', 'HVAC maintenance', 'Heat pumps'],
    ],
    [
      '@context' => 'https://schema.org',
      '@type' => 'WebSite',
      'url' => $base . '/es/',
      'inLanguage' => $lang,
    ],
    [
      '@context' => 'https://schema.org',
      '@type' => 'BreadcrumbList',
      'itemListElement' => $breadcrumbItems,
    ],
  ];

  return '<script type="application/ld+json">' .
    json_encode($items, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .
    '</script>';
}

function snapshot_feedback_script(): string {
  $sent = $_GET['sent'] ?? null;
  if ($sent === null) {
    return '';
  }
  $modal = $sent === '1' ? 'thanksModal' : 'errorModal';
  return '<script>document.addEventListener("DOMContentLoaded",function(){var el=document.getElementById("' .
    $modal .
    '");if(el&&window.bootstrap){new bootstrap.Modal(el).show();}});</script>';
}

function render_es_hub_page(string $path): ?string {
  $servicePage = es_service_page_for_path($path);
  if ($servicePage !== null) {
    return render_es_legacy_shell($servicePage, $path, render_service_detail_body($servicePage));
  }

  $pages = [
    '/es/servicios/' => [
      'slug' => 'servicios',
      'title' => 'Servicios de climatizaci&oacute;n en Benidorm y Marina Baixa | +QUECLIMA',
      'description' => 'Instalaci&oacute;n, mantenimiento y reparaci&oacute;n de aire acondicionado, bomba de calor y energ&iacute;a solar en Benidorm, Marina Baixa y Alicante.',
      'h1' => 'Servicios de climatizaci&oacute;n en Benidorm y Marina Baixa',
      'breadcrumb' => 'Servicios',
      'hero_image' => '/assets/img/heroes/hub-servicios-climatizacion.webp',
      'hero_alt' => 'Servicios de climatizaci&oacute;n en Benidorm y Marina Baixa',
      'visual_slot' => 'hub-servicios-hero',
      'image_position' => 'center center',
    ],
    '/es/zonas/' => [
      'slug' => 'zonas',
      'title' => 'Servicio de climatizaci&oacute;n por zonas en Alicante | +QUECLIMA',
      'description' => 'Cobertura local de climatizaci&oacute;n en Benidorm, Altea, Calpe, Finestrat, La Nuc&iacute;a y otras zonas de Alicante.',
      'h1' => 'Servicio de climatizaci&oacute;n por zonas en Alicante',
      'breadcrumb' => 'Zonas',
      'hero_image' => '/assets/img/heroes/hub-zonas-marina-baixa.webp',
      'hero_alt' => 'Servicio de climatizaci&oacute;n por zonas en Alicante',
      'visual_slot' => 'hub-zonas-hero',
      'image_position' => 'center center',
    ],
    '/es/blog/' => [
      'slug' => 'blog',
      'title' => 'Gu&iacute;as de climatizaci&oacute;n, aerotermia y aire acondicionado | +QUECLIMA',
      'description' => 'Gu&iacute;as pr&aacute;cticas sobre climatizaci&oacute;n, aerotermia, bomba de calor, mantenimiento, consumo e instalaci&oacute;n en la Costa Blanca.',
      'h1' => 'Gu&iacute;as de climatizaci&oacute;n, aerotermia y aire acondicionado',
      'breadcrumb' => 'Gu&iacute;as',
      'hero_image' => '/assets/img/heroes/hub-guias-climatizacion.webp',
      'hero_alt' => 'Gu&iacute;as de climatizaci&oacute;n y aire acondicionado',
      'visual_slot' => 'hub-guias-hero',
      'image_position' => 'center center',
    ],
  ];

  if (!isset($pages[$path])) {
    return null;
  }

  $page = $pages[$path];
  $body = match ($page['slug']) {
    'servicios' => hub_services_body($page),
    'zonas' => hub_zones_body($page),
    'blog' => hub_blog_body($page),
    default => '',
  };

  return render_es_legacy_shell($page, $path, $body);
}

function render_es_legacy_shell(array $page, string $path, string $body): string {
  $homeSnapshot = snapshot_file_for_path('/es/');
  if ($homeSnapshot === null) {
    return render_es_minimal_shell($page, $path, $body);
  }

  $base = file_get_contents($homeSnapshot);
  if ($base === false) {
    return render_es_minimal_shell($page, $path, $body);
  }

  $mainOpen = '<main id="main-content">';
  $mainStart = strpos($base, $mainOpen);
  if ($mainStart === false) {
    return render_es_minimal_shell($page, $path, $body);
  }

  $mainEnd = strpos($base, '</main>', $mainStart);
  if ($mainEnd === false) {
    return render_es_minimal_shell($page, $path, $body);
  }

  $headAndHeader = substr($base, 0, $mainStart + strlen($mainOpen));
  $interactiveTail = legacy_es_interactive_main_tail($base, $mainStart + strlen($mainOpen), $mainEnd);
  $tail = substr($base, $mainEnd);
  $headAndHeader = patch_es_hub_head($headAndHeader, $page, $path);
  $finalCta = final_budget_cta_html('es');

  return $headAndHeader . "\n" . $body . "\n" . $finalCta . "\n" . $interactiveTail . "\n" . $tail;
}

function legacy_es_interactive_main_tail(string $base, int $mainContentStart, int $mainEnd): string {
  $mainContent = substr($base, $mainContentStart, $mainEnd - $mainContentStart);
  $markers = [
    '<!--' . "\n" . '  WhatsApp FAB',
    '<a href="https://wa.me/34613026600" class="btn-whatsapp-pulse',
    '<div class="modal fade quote-modal" id="quoteModal"',
  ];

  foreach ($markers as $marker) {
    $pos = strpos($mainContent, $marker);
    if ($pos !== false) {
      return substr($mainContent, $pos);
    }
  }

  return '';
}

function final_budget_cta_html(string $lang): string {
  return render_partial('final_budget_cta', ['lang' => $lang]);
}

function es_service_page_for_path(string $path): ?array {
  $pages = es_service_pages();
  return $pages[$path] ?? null;
}

function es_service_pages(): array {
  static $pages = null;
  if ($pages !== null) {
    return $pages;
  }

  $file = __DIR__ . '/content/services/es.php';
  $loaded = is_file($file) ? require $file : [];
  $pages = is_array($loaded) ? $loaded : [];

  return $pages;
}

function render_service_detail_body(array $service, string $lang = 'es'): string {
  $localityPrefix = match($lang) {
    'en'    => '/en/air-conditioning-',
    'de'    => '/de/klimaanlage-',
    'nl'    => '/nl/airco-',
    'ru'    => '/ru/konditsioner-',
    'no'    => '/no/aircondition-',
    default => '/es/aire-acondicionado-',
  };
  $priorityZones = [
    ['Benidorm',         $localityPrefix . 'benidorm/'],
    ['Altea',            $localityPrefix . 'altea/'],
    ['Calpe',            $localityPrefix . 'calpe/'],
    ['Finestrat',        $localityPrefix . 'finestrat/'],
    ['La Nuc&iacute;a',  $localityPrefix . 'la-nucia/'],
  ];
  if ($lang === 'es') {
    $otherServices = [
      ['Instalaci&oacute;n de aire acondicionado', '/es/servicios/instalacion-aire-acondicionado/'],
      ['Mantenimiento de climatizaci&oacute;n',    '/es/servicios/mantenimiento-climatizacion/'],
      ['Reparaci&oacute;n de aire acondicionado',  '/es/servicios/reparacion-aire-acondicionado/'],
      ['Aerotermia y bomba de calor',              '/es/servicios/aerotermia-bomba-calor/'],
      ['Energ&iacute;a solar t&eacute;rmica',      '/es/servicios/energia-solar-termica/'],
    ];
  } else {
    $otherServices = array_values(array_map(
      fn(array $p): array => [$p['breadcrumb'], $p['path']],
      lang_service_pages($lang)
    ));
  }
  $servicesHubUrl = localized_hub_url($lang, 'services') ?? '/es/servicios/';
  $zonesHubUrl    = localized_hub_url($lang, 'zones')    ?? '/es/zonas/';
  static $uiLabels = [
    'es' => [
      'service_kicker'  => 'Servicio',
      'cta_quote'       => 'Pedir presupuesto',
      'cta_zones'       => 'Ver zonas de servicio',
      'process_kicker'  => 'Proceso',
      'zones_kicker'    => 'Zonas',
      'zones_title'     => 'Zonas donde prestamos servicio',
      'all_services'    => 'Todos los servicios',
      'all_zones'       => 'Todas las zonas',
      'other_kicker'    => 'Otros servicios',
      'related_title'   => 'Servicios relacionados',
      'faq_kicker'      => 'Dudas habituales',
      'faq_title'       => 'Preguntas frecuentes',
    ],
    'en' => [
      'service_kicker'  => 'Service',
      'cta_quote'       => 'Get a quote',
      'cta_zones'       => 'View service areas',
      'process_kicker'  => 'Process',
      'zones_kicker'    => 'Areas',
      'zones_title'     => 'Areas where we work',
      'all_services'    => 'All services',
      'all_zones'       => 'All areas',
      'other_kicker'    => 'Other services',
      'related_title'   => 'Related services',
      'faq_kicker'      => 'Common questions',
      'faq_title'       => 'Frequently asked questions',
    ],
    'de' => [
      'service_kicker'  => 'Leistung',
      'cta_quote'       => 'Angebot anfordern',
      'cta_zones'       => 'Servicegebiete ansehen',
      'process_kicker'  => 'Ablauf',
      'zones_kicker'    => 'Gebiete',
      'zones_title'     => 'Gebiete, in denen wir t&auml;tig sind',
      'all_services'    => 'Alle Leistungen',
      'all_zones'       => 'Alle Gebiete',
      'other_kicker'    => 'Weitere Leistungen',
      'related_title'   => '&Auml;hnliche Leistungen',
      'faq_kicker'      => 'H&auml;ufige Fragen',
      'faq_title'       => 'H&auml;ufig gestellte Fragen',
    ],
    'nl' => [
      'service_kicker'  => 'Dienst',
      'cta_quote'       => 'Offerte aanvragen',
      'cta_zones'       => 'Servicegebieden bekijken',
      'process_kicker'  => 'Werkwijze',
      'zones_kicker'    => 'Gebieden',
      'zones_title'     => 'Gebieden waar wij actief zijn',
      'all_services'    => 'Alle diensten',
      'all_zones'       => 'Alle gebieden',
      'other_kicker'    => 'Andere diensten',
      'related_title'   => 'Gerelateerde diensten',
      'faq_kicker'      => 'Veelgestelde vragen',
      'faq_title'       => 'Veelgestelde vragen',
    ],
    'ru' => [
      'service_kicker'  => '&#1059;&#1089;&#1083;&#1091;&#1075;&#1072;',
      'cta_quote'       => '&#1055;&#1086;&#1083;&#1091;&#1095;&#1080;&#1090;&#1100; &#1087;&#1088;&#1077;&#1076;&#1083;&#1086;&#1078;&#1077;&#1085;&#1080;&#1077;',
      'cta_zones'       => '&#1055;&#1086;&#1089;&#1084;&#1086;&#1090;&#1088;&#1077;&#1090;&#1100; &#1088;&#1072;&#1081;&#1086;&#1085;&#1099;',
      'process_kicker'  => '&#1055;&#1088;&#1086;&#1094;&#1077;&#1089;&#1089;',
      'zones_kicker'    => '&#1056;&#1072;&#1081;&#1086;&#1085;&#1099;',
      'zones_title'     => '&#1056;&#1072;&#1081;&#1086;&#1085;&#1099; &#1085;&#1072;&#1096;&#1077;&#1081; &#1088;&#1072;&#1073;&#1086;&#1090;&#1099;',
      'all_services'    => '&#1042;&#1089;&#1077; &#1091;&#1089;&#1083;&#1091;&#1075;&#1080;',
      'all_zones'       => '&#1042;&#1089;&#1077; &#1088;&#1072;&#1081;&#1086;&#1085;&#1099;',
      'other_kicker'    => '&#1044;&#1088;&#1091;&#1075;&#1080;&#1077; &#1091;&#1089;&#1083;&#1091;&#1075;&#1080;',
      'related_title'   => '&#1055;&#1086;&#1093;&#1086;&#1078;&#1080;&#1077; &#1091;&#1089;&#1083;&#1091;&#1075;&#1080;',
      'faq_kicker'      => '&#1063;&#1072;&#1089;&#1090;&#1099;&#1077; &#1074;&#1086;&#1087;&#1088;&#1086;&#1089;&#1099;',
      'faq_title'       => '&#1063;&#1072;&#1089;&#1090;&#1086; &#1079;&#1072;&#1076;&#1072;&#1074;&#1072;&#1077;&#1084;&#1099;&#1077; &#1074;&#1086;&#1087;&#1088;&#1086;&#1089;&#1099;',
    ],
    'no' => [
      'service_kicker'  => 'Tjeneste',
      'cta_quote'       => 'F&aring; tilbud',
      'cta_zones'       => 'Se serviceomr&aring;der',
      'process_kicker'  => 'Prosess',
      'zones_kicker'    => 'Omr&aring;der',
      'zones_title'     => 'Omr&aring;der vi betjener',
      'all_services'    => 'Alle tjenester',
      'all_zones'       => 'Alle omr&aring;der',
      'other_kicker'    => 'Andre tjenester',
      'related_title'   => 'Relaterte tjenester',
      'faq_kicker'      => 'Vanlige sp&oslash;rsm&aring;l',
      'faq_title'       => 'Ofte stilte sp&oslash;rsm&aring;l',
    ],
  ];
  $serviceUi = $uiLabels[$lang] ?? $uiLabels['es'];
  ob_start();
  include __DIR__ . '/../views/service_detail.php';
  return (string) ob_get_clean();
}

function render_es_minimal_shell(array $page, string $path, string $body): string {
  $canonical = 'https://masqueclima.es' . $path;
  return '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">' .
    '<title>' . $page['title'] . '</title><meta name="description" content="' . $page['description'] . '">' .
    '<link rel="canonical" href="' . $canonical . '"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">' .
    '<link rel="stylesheet" href="/assets/css/styles.css">' . es_page_jsonld($path, $page) . '</head><body><main id="main-content">' .
    $body . final_budget_cta_html('es') . '</main><script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>';
}

function patch_es_hub_head(string $html, array $page, string $path): string {
  $canonical = 'https://masqueclima.es' . $path;

  $html = preg_replace('/<title>.*?<\/title>/is', '<title>' . $page['title'] . '</title>', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="description" content="[^"]*">/i', '<meta name="description" content="' . $page['description'] . '">', $html, 1) ?? $html;
  $html = preg_replace('/<link rel="canonical" href="[^"]*">/i', '<link rel="canonical" href="' . $canonical . '">', $html, 1) ?? $html;
  $html = preg_replace('/<!-- Hreflang -->\s*(?:<link rel="alternate"[^>]+>\s*)+/i', '', $html, 1) ?? $html;
  $html = preg_replace('/<meta property="og:title" content="[^"]*">/i', '<meta property="og:title" content="' . $page['title'] . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta property="og:description" content="[^"]*">/i', '<meta property="og:description" content="' . $page['description'] . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta property="og:url" content="[^"]*">/i', '<meta property="og:url" content="' . $canonical . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="twitter:title" content="[^"]*">/i', '<meta name="twitter:title" content="' . $page['title'] . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="twitter:description" content="[^"]*">/i', '<meta name="twitter:description" content="' . $page['description'] . '">', $html, 1) ?? $html;
  $html = patch_es_hub_hero_preload($html, $page);
  $html = str_replace('</head>', es_page_jsonld($path, $page) . "\n</head>", $html);

  return $html;
}

function patch_es_hub_hero_preload(string $html, array $page): string {
  $hero = $page['hero_image'] ?? null;
  if (!public_asset_exists(is_string($hero) ? $hero : null)) {
    return $html;
  }

  $hero = (string) $hero;
  $html = preg_replace(
    '/\s*<link rel="preload" as="image" href="\/assets\/img\/hero\.jpg"[^>]*>\s*/i',
    "\n",
    $html,
    1
  ) ?? $html;

  $preload = '<link rel="preload" as="image" href="' . e($hero) . '" imagesizes="100vw" fetchpriority="high">';

  return preg_replace(
    '/\s*<!-- Preload hero responsive[\s\S]*?<link rel="preload" as="image"[\s\S]*?fetchpriority="high">\s*/i',
    "\n  " . $preload . "\n",
    $html,
    1
  ) ?? $html;
}

function es_page_jsonld(string $path, array $page): string {
  $base = 'https://masqueclima.es';
  $breadcrumbName = (string) ($page['breadcrumb'] ?? 'Pagina');
  $itemList = [[
    '@type' => 'ListItem',
    'position' => 1,
    'name' => 'Inicio',
    'item' => $base . '/es/',
  ]];

  if (str_starts_with($path, '/es/servicios/') && $path !== '/es/servicios/') {
    $itemList[] = [
      '@type' => 'ListItem',
      'position' => 2,
      'name' => 'Servicios',
      'item' => $base . '/es/servicios/',
    ];
  }

  $itemList[] = [
    '@type' => 'ListItem',
    'position' => count($itemList) + 1,
    'name' => html_entity_decode($breadcrumbName, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
    'item' => $base . $path,
  ];

  $schemas = [[
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => $itemList,
  ]];

  if (!empty($page['service_type'])) {
    $schemas[] = [
      '@context' => 'https://schema.org',
      '@type' => 'Service',
      'name' => html_entity_decode((string) $page['service_type'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
      'description' => html_entity_decode((string) $page['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
      'url' => $base . $path,
      'provider' => [
        '@type' => 'HVACBusiness',
        'name' => '+QUECLIMA',
        'telephone' => '+34 613 02 66 00',
        'url' => $base . '/es/',
      ],
      'areaServed' => ['Benidorm', 'Altea', 'Calpe', 'Finestrat', 'La Nucia', 'Marina Baixa', 'Alicante'],
      'serviceType' => html_entity_decode((string) $page['service_type'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
    ];
  }

  if (!empty($page['faq']) && is_array($page['faq'])) {
    $faqItems = [];
    foreach ($page['faq'] as $item) {
      if (!empty($item['q']) && !empty($item['a'])) {
        $faqItems[] = [
          '@type' => 'Question',
          'name' => html_entity_decode((string) $item['q'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
          'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => html_entity_decode((string) $item['a'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
          ],
        ];
      }
    }
    if (!empty($faqItems)) {
      $schemas[] = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqItems,
      ];
    }
  }

  return '<script type="application/ld+json">' .
    json_encode($schemas, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .
    '</script>';
}

function es_hub_jsonld(string $path, string $name): string {
  $base = 'https://masqueclima.es';
  $data = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      [
        '@type' => 'ListItem',
        'position' => 1,
        'name' => 'Inicio',
        'item' => $base . '/es/',
      ],
      [
        '@type' => 'ListItem',
        'position' => 2,
        'name' => $name,
        'item' => $base . $path,
      ],
    ],
  ];

  return '<script type="application/ld+json">' .
    json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .
    '</script>';
}

function hub_intro(string $h1, string $text, array $visual = []): string {
  return render_partial('hub_hero', array_merge(['h1' => $h1, 'text' => $text], $visual));
}

function hub_styles(): string {
  return render_partial('hub_styles');
}

function hub_visual_options(array $page): array {
  return [
    'hero_image' => $page['hero_image'] ?? null,
    'hero_alt' => $page['hero_alt'] ?? ($page['h1'] ?? '+QUECLIMA climatizaci&oacute;n en Alicante'),
    'visual_slot' => $page['visual_slot'] ?? '',
    'overlay' => $page['hero_overlay'] ?? 'soft',
    'image_position' => $page['image_position'] ?? 'center center',
  ];
}

function hub_services_body(array $page): string {
  $intro = hub_intro(
    'Servicios de climatizaci&oacute;n en Benidorm y Marina Baixa',
    'Instalamos, mantenemos y reparamos sistemas de aire acondicionado, calefacci&oacute;n y energ&iacute;a para viviendas, apartamentos tur&iacute;sticos, comunidades y negocios.',
    hub_visual_options($page)
  );
  $styles = hub_styles();
  $serviceCards = render_partial('service_cards');
  $miniMap = render_partial('zone_visual', ['variant' => 'mini']);

  return <<<HTML
{$styles}
{$intro}
<section class="hub-section" aria-labelledby="servicios-principales">
  <div class="container">
    <p class="hub-kicker">Servicios</p>
    <h2 class="section-title" id="servicios-principales">Soluciones de climatizaci&oacute;n</h2>
    <p class="hub-muted">Trabajamos con equipos split, multisplit, conductos y bomba de calor. Antes de instalar revisamos la vivienda o el local para recomendar una soluci&oacute;n eficiente y ajustada al uso real.</p>
    {$serviceCards}
  </div>
</section>
<section class="hub-section alt" aria-labelledby="zonas-destacadas">
  <div class="container">
    <p class="hub-kicker">Zonas destacadas</p>
    <h2 class="section-title" id="zonas-destacadas">Servicio local en la Marina Baixa</h2>
    <div class="service-zones-panel">
      <div class="service-zones-copy">
        <p class="hub-muted">Trabajamos a diario en Benidorm, Altea, Calpe, Finestrat y La Nuc&iacute;a, y tambi&eacute;n nos desplazamos a otras localidades cercanas.</p>
        <div class="hub-links">
          <a class="hub-pill" href="/es/aire-acondicionado-benidorm/">Aire acondicionado en Benidorm</a>
          <a class="hub-pill" href="/es/aire-acondicionado-altea/">Aire acondicionado en Altea</a>
          <a class="hub-pill" href="/es/aire-acondicionado-calpe/">Aire acondicionado en Calpe</a>
          <a class="hub-pill" href="/es/aire-acondicionado-finestrat/">Aire acondicionado en Finestrat</a>
          <a class="hub-pill" href="/es/aire-acondicionado-la-nucia/">Aire acondicionado en La Nuc&iacute;a</a>
          <a class="hub-pill" href="/es/zonas/">Todas las zonas</a>
        </div>
      </div>
      {$miniMap}
    </div>
  </div>
</section>
HTML;
}

function hub_zones_body(array $page): string {
  $intro = hub_intro(
    'Servicio de climatizaci&oacute;n por zonas en Alicante',
    'Trabajamos desde Benidorm para la Marina Baixa, Costa Blanca norte y provincia de Alicante, con desplazamiento r&aacute;pido y asesoramiento cercano.',
    hub_visual_options($page)
  );
  $styles = hub_styles();
  $zoneMap = render_partial('zone_visual');
  $zones = [
    ['Albir', '/es/aire-acondicionado-albir/'],
    ['Alfaz del Pi', '/es/aire-acondicionado-alfaz-del-pi/'],
    ['Altea', '/es/aire-acondicionado-altea/'],
    ['Beniard&agrave;', '/es/aire-acondicionado-beniarda/'],
    ['Benidorm', '/es/aire-acondicionado-benidorm/'],
    ['Benifato', '/es/aire-acondicionado-benifato/'],
    ['Benimantell', '/es/aire-acondicionado-benimantell/'],
    ['Bolulla', '/es/aire-acondicionado-bolulla/'],
    ['Callosa d&rsquo;en Sarri&agrave;', '/es/aire-acondicionado-callosa-den-sarria/'],
    ['Calpe', '/es/aire-acondicionado-calpe/'],
    ['Confrides', '/es/aire-acondicionado-confrides/'],
    ['Finestrat', '/es/aire-acondicionado-finestrat/'],
    ['Guadalest', '/es/aire-acondicionado-guadalest/'],
    ['La Nuc&iacute;a', '/es/aire-acondicionado-la-nucia/'],
    ['Orxeta', '/es/aire-acondicionado-orxeta/'],
    ['Polop', '/es/aire-acondicionado-polop/'],
    ['Relleu', '/es/aire-acondicionado-relleu/'],
    ['Sella', '/es/aire-acondicionado-sella/'],
    ['T&agrave;rbena', '/es/aire-acondicionado-tarbena/'],
    ['Villajoyosa', '/es/aire-acondicionado-villajoyosa/'],
  ];

  $items = '';
  foreach ($zones as [$name, $url]) {
    $items .= '<a class="hub-pill" href="' . $url . '">' . $name . '</a>' . "\n";
  }

  return <<<HTML
{$styles}
{$intro}
<section class="hub-section" aria-labelledby="zonas-listado">
  <div class="container">
    <p class="hub-kicker">Cobertura</p>
    <h2 class="section-title" id="zonas-listado">Localidades donde prestamos servicio</h2>
    <p class="hub-muted">Realizamos instalaciones, mantenimiento y reparaciones de climatizaci&oacute;n en estas localidades y alrededores.</p>
    <div class="zone-layout">
      {$zoneMap}
      <div class="zone-list-card">
        <p class="hub-muted">Selecciona una localidad para ver la p&aacute;gina local correspondiente.</p>
        <div class="hub-links zone-chip-grid">
          {$items}
        </div>
        <div class="hub-cta">
          <a class="btn btn-primary js-track" data-ev="cta_quote_zones" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote">Pedir presupuesto</a>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="hub-section alt" aria-labelledby="zonas-contexto">
  <div class="container">
    <p class="hub-kicker">Prioridad local</p>
    <h2 class="section-title" id="zonas-contexto">Benidorm, Marina Baixa y Costa Blanca norte</h2>
    <p class="hub-muted">Cada zona tiene necesidades distintas: apartamentos tur&iacute;sticos en Benidorm, viviendas cerca del mar en Altea y Calpe, obra nueva en Finestrat o chalets en La Nuc&iacute;a. Adaptamos la soluci&oacute;n a cada vivienda y a cada uso.</p>
  </div>
</section>
HTML;
}

function hub_blog_body(array $page): string {
  $intro = hub_intro(
    'Gu&iacute;as de climatizaci&oacute;n, aerotermia y aire acondicionado',
    'Consejos pr&aacute;cticos para elegir, mantener y aprovechar mejor tu sistema de climatizaci&oacute;n en la Costa Blanca.',
    hub_visual_options($page)
  );
  $styles = hub_styles();
  $guideCards = render_partial('guide_cards');

  return <<<HTML
{$styles}
{$intro}
<section class="hub-section" aria-labelledby="blog-plan">
  <div class="container">
    <p class="hub-kicker">Gu&iacute;as</p>
    <h2 class="section-title" id="blog-plan">Biblioteca de gu&iacute;as &uacute;tiles</h2>
    <p class="hub-muted">Estamos preparando gu&iacute;as &uacute;tiles basadas en dudas reales de clientes: consumo, potencia necesaria, mantenimiento, bomba de calor, aerotermia, instalaci&oacute;n en viviendas, apartamentos tur&iacute;sticos y comunidades.</p>
    {$guideCards}
  </div>
</section>
HTML;
}

function locality_hero_map(): array {
  return [
    'albir'              => '/assets/img/localidades/heroes/hero-localidad-albir.webp',
    'alfaz-del-pi'       => '/assets/img/localidades/heroes/hero-localidad-alfaz-del-pi.webp',
    'altea'              => '/assets/img/localidades/heroes/hero-localidad-altea.webp',
    'beniarda'           => '/assets/img/localidades/heroes/hero-localidad-beniarda.webp',
    'benidorm'           => '/assets/img/localidades/heroes/hero-localidad-benidorm.webp',
    'benifato'           => '/assets/img/localidades/heroes/hero-localidad-benifato.webp',
    'benimantell'        => '/assets/img/localidades/heroes/hero-localidad-benimantell.webp',
    'bolulla'            => '/assets/img/localidades/heroes/hero-localidad-bolulla.webp',
    'callosa-den-sarria' => '/assets/img/localidades/heroes/hero-localidad-callosa-den-sarria.webp',
    'calpe'              => '/assets/img/localidades/heroes/hero-localidad-calpe.webp',
    'confrides'          => '/assets/img/localidades/heroes/hero-localidad-confrides.webp',
    'finestrat'          => '/assets/img/localidades/heroes/hero-localidad-finestrat.webp',
    'guadalest'          => '/assets/img/localidades/heroes/hero-localidad-guadalest.webp',
    'la-nucia'           => '/assets/img/localidades/heroes/hero-localidad-la-nucia.webp',
    'orxeta'             => '/assets/img/localidades/heroes/hero-localidad-orxeta.webp',
    'polop'              => '/assets/img/localidades/heroes/hero-localidad-polop.webp',
    'relleu'             => '/assets/img/localidades/heroes/hero-localidad-relleu.webp',
    'sella'              => '/assets/img/localidades/heroes/hero-localidad-sella.webp',
    'tarbena'            => '/assets/img/localidades/heroes/hero-localidad-tarbena.webp',
    'villajoyosa'        => '/assets/img/localidades/heroes/hero-localidad-villajoyosa.webp',
  ];
}

function patch_locality_hero_image(string $html, string $path, string $lang): string {
  $langPatterns = [
    'es' => '~^/es/aire-acondicionado-([a-z0-9-]+)/$~',
    'en' => '~^/en/air-conditioning-([a-z0-9-]+)/$~',
    'de' => '~^/de/klimaanlage-([a-z0-9-]+)/$~',
    'nl' => '~^/nl/airco-([a-z0-9-]+)/$~',
    'ru' => '~^/ru/konditsioner-([a-z0-9-]+)/$~',
    'no' => '~^/no/aircondition-([a-z0-9-]+)/$~',
  ];

  $pattern = $langPatterns[$lang] ?? null;
  if ($pattern === null) {
    return $html;
  }

  if (!preg_match($pattern, $path, $m)) {
    return $html;
  }

  $slug = $m[1];
  $heroMap = locality_hero_map();

  if (!isset($heroMap[$slug])) {
    return $html;
  }

  $heroPath = $heroMap[$slug];

  if (!public_asset_exists($heroPath)) {
    return $html;
  }

  // Replace src in the hero-img element (handles multiline attribute layout)
  $html = preg_replace(
    '/(<img\s[^>]*class="hero-img"[^>]*src=")[^"]*(")/s',
    '$1' . e($heroPath) . '$2',
    $html,
    1
  ) ?? $html;

  // Remove legacy hero.jpg preload
  $html = preg_replace(
    '/\s*<link rel="preload" as="image" href="\/assets\/img\/hero\.jpg"[^>]*>\s*/i',
    "\n",
    $html,
    1
  ) ?? $html;

  // Replace responsive preload block with specific local hero preload
  $preload = '<link rel="preload" as="image" href="' . e($heroPath) . '" imagesizes="100vw" fetchpriority="high">';
  $html = preg_replace(
    '/\s*<!-- Preload hero responsive[\s\S]*?<link rel="preload" as="image"[\s\S]*?fetchpriority="high">\s*/i',
    "\n  " . $preload . "\n",
    $html,
    1
  ) ?? $html;

  return $html;
}

function patch_es_p1_location_page(string $html, string $path, string $lang): string {
  if ($lang !== 'es') {
    return $html;
  }

  $pages = [
    '/es/aire-acondicionado-benidorm/' => [
      'city' => 'Benidorm',
      'title' => 'Aire acondicionado en Benidorm: instalaci&oacute;n y mantenimiento | +QUECLIMA',
      'description' => 'Instalaci&oacute;n, reparaci&oacute;n y mantenimiento de aire acondicionado en Benidorm para viviendas, apartamentos tur&iacute;sticos y comunidades.',
      'nearby' => [
        ['Finestrat', '/es/aire-acondicionado-finestrat/'],
        ['La Nuc&iacute;a', '/es/aire-acondicionado-la-nucia/'],
        ['Albir', '/es/aire-acondicionado-albir/'],
      ],
    ],
    '/es/aire-acondicionado-altea/' => [
      'city' => 'Altea',
      'title' => 'Aire acondicionado en Altea: instalaci&oacute;n y mantenimiento | +QUECLIMA',
      'description' => 'Climatizaci&oacute;n en Altea para viviendas, villas y apartamentos cerca del mar, con instalaci&oacute;n limpia, mantenimiento y reparaci&oacute;n.',
      'nearby' => [
        ['Albir', '/es/aire-acondicionado-albir/'],
        ['Calpe', '/es/aire-acondicionado-calpe/'],
        ['La Nuc&iacute;a', '/es/aire-acondicionado-la-nucia/'],
      ],
    ],
    '/es/aire-acondicionado-calpe/' => [
      'city' => 'Calpe',
      'title' => 'Aire acondicionado en Calpe: instalaci&oacute;n y mantenimiento | +QUECLIMA',
      'description' => 'Servicio de aire acondicionado en Calpe para apartamentos, comunidades y viviendas de costa: instalaci&oacute;n, mantenimiento y aver&iacute;as.',
      'nearby' => [
        ['Altea', '/es/aire-acondicionado-altea/'],
        ['Finestrat', '/es/aire-acondicionado-finestrat/'],
        ['Villajoyosa', '/es/aire-acondicionado-villajoyosa/'],
      ],
    ],
    '/es/aire-acondicionado-finestrat/' => [
      'city' => 'Finestrat',
      'title' => 'Aire acondicionado en Finestrat: instalaci&oacute;n y conductos | +QUECLIMA',
      'description' => 'Instalaci&oacute;n y mantenimiento de aire acondicionado en Finestrat, obra nueva y Balc&oacute;n de Finestrat, con revisi&oacute;n de preinstalaci&oacute;n.',
      'nearby' => [
        ['Benidorm', '/es/aire-acondicionado-benidorm/'],
        ['La Nuc&iacute;a', '/es/aire-acondicionado-la-nucia/'],
        ['Polop', '/es/aire-acondicionado-polop/'],
      ],
    ],
    '/es/aire-acondicionado-la-nucia/' => [
      'city' => 'La Nuc&iacute;a',
      'title' => 'Aire acondicionado en La Nuc&iacute;a: chalets y bomba de calor | +QUECLIMA',
      'description' => 'Climatizaci&oacute;n en La Nuc&iacute;a para chalets y viviendas de dos plantas: aire acondicionado, bomba de calor, mantenimiento y reparaci&oacute;n.',
      'nearby' => [
        ['Polop', '/es/aire-acondicionado-polop/'],
        ['Alfaz del Pi', '/es/aire-acondicionado-alfaz-del-pi/'],
        ['Benidorm', '/es/aire-acondicionado-benidorm/'],
      ],
    ],
    '/es/aire-acondicionado-albir/' => [
      'city' => 'Albir',
      'title' => 'Aire acondicionado en Albir: instalaci&oacute;n y mantenimiento | +QUECLIMA',
      'description' => 'Instalaci&oacute;n y mantenimiento de aire acondicionado en Albir para apartamentos, casas y locales en la Costa Blanca.',
      'nearby' => [
        ['Altea', '/es/aire-acondicionado-altea/'],
        ['Alfaz del Pi', '/es/aire-acondicionado-alfaz-del-pi/'],
        ['La Nuc&iacute;a', '/es/aire-acondicionado-la-nucia/'],
      ],
    ],
    '/es/aire-acondicionado-alfaz-del-pi/' => [
      'city' => 'Alfaz del Pi',
      'title' => 'Aire acondicionado en Alfaz del Pi: instalaci&oacute;n y servicio | +QUECLIMA',
      'description' => 'Servicio de aire acondicionado en Alfaz del Pi: instalaci&oacute;n, mantenimiento y reparaci&oacute;n para viviendas y apartamentos en la Marina Baixa.',
      'nearby' => [
        ['Albir', '/es/aire-acondicionado-albir/'],
        ['La Nuc&iacute;a', '/es/aire-acondicionado-la-nucia/'],
        ['Altea', '/es/aire-acondicionado-altea/'],
      ],
    ],
    '/es/aire-acondicionado-beniarda/' => [
      'city' => 'Beniard&agrave;',
      'title' => 'Aire acondicionado en Beniard&agrave;: instalaci&oacute;n y mantenimiento | +QUECLIMA',
      'description' => 'Instalaci&oacute;n y mantenimiento de aire acondicionado en Beniard&agrave; para viviendas de interior y chalets en la Sierra de Aitana.',
      'nearby' => [
        ['Guadalest', '/es/aire-acondicionado-guadalest/'],
        ['Polop', '/es/aire-acondicionado-polop/'],
        ['Callosa d&rsquo;en Sarri&agrave;', '/es/aire-acondicionado-callosa-den-sarria/'],
      ],
    ],
    '/es/aire-acondicionado-benifato/' => [
      'city' => 'Benifato',
      'title' => 'Aire acondicionado en Benifato: instalaci&oacute;n y servicio | +QUECLIMA',
      'description' => 'Servicio de instalaci&oacute;n y mantenimiento de aire acondicionado en Benifato para viviendas en la comarca de la Marina Baixa.',
      'nearby' => [
        ['Guadalest', '/es/aire-acondicionado-guadalest/'],
        ['Callosa d&rsquo;en Sarri&agrave;', '/es/aire-acondicionado-callosa-den-sarria/'],
        ['Relleu', '/es/aire-acondicionado-relleu/'],
      ],
    ],
    '/es/aire-acondicionado-benimantell/' => [
      'city' => 'Benimantell',
      'title' => 'Aire acondicionado en Benimantell: instalaci&oacute;n y mantenimiento | +QUECLIMA',
      'description' => 'Instalaci&oacute;n y mantenimiento de aire acondicionado en Benimantell para viviendas rurales y chalets en la zona de Guadalest.',
      'nearby' => [
        ['Guadalest', '/es/aire-acondicionado-guadalest/'],
        ['Benifato', '/es/aire-acondicionado-benifato/'],
        ['Callosa d&rsquo;en Sarri&agrave;', '/es/aire-acondicionado-callosa-den-sarria/'],
      ],
    ],
    '/es/aire-acondicionado-bolulla/' => [
      'city' => 'Bolulla',
      'title' => 'Aire acondicionado en Bolulla: instalaci&oacute;n y servicio | +QUECLIMA',
      'description' => 'Servicio de aire acondicionado en Bolulla para viviendas y casas de campo en la comarca de la Marina Baixa.',
      'nearby' => [
        ['Callosa d&rsquo;en Sarri&agrave;', '/es/aire-acondicionado-callosa-den-sarria/'],
        ['T&agrave;rbena', '/es/aire-acondicionado-tarbena/'],
        ['Polop', '/es/aire-acondicionado-polop/'],
      ],
    ],
    '/es/aire-acondicionado-callosa-den-sarria/' => [
      'city' => 'Callosa d&rsquo;en Sarri&agrave;',
      'title' => 'Aire acondicionado en Callosa d&rsquo;en Sarri&agrave;: instalaci&oacute;n | +QUECLIMA',
      'description' => 'Instalaci&oacute;n, mantenimiento y reparaci&oacute;n de aire acondicionado en Callosa d&rsquo;en Sarri&agrave; para viviendas y negocios en la Marina Baixa.',
      'nearby' => [
        ['Polop', '/es/aire-acondicionado-polop/'],
        ['La Nuc&iacute;a', '/es/aire-acondicionado-la-nucia/'],
        ['Guadalest', '/es/aire-acondicionado-guadalest/'],
      ],
    ],
    '/es/aire-acondicionado-confrides/' => [
      'city' => 'Confrides',
      'title' => 'Aire acondicionado en Confrides: instalaci&oacute;n y servicio | +QUECLIMA',
      'description' => 'Servicio de instalaci&oacute;n y mantenimiento de aire acondicionado en Confrides para viviendas en la comarca de El Comtat y Sierra de Aitana.',
      'nearby' => [
        ['Guadalest', '/es/aire-acondicionado-guadalest/'],
        ['Beniard&agrave;', '/es/aire-acondicionado-beniarda/'],
        ['Callosa d&rsquo;en Sarri&agrave;', '/es/aire-acondicionado-callosa-den-sarria/'],
      ],
    ],
    '/es/aire-acondicionado-guadalest/' => [
      'city' => 'Guadalest',
      'title' => 'Aire acondicionado en Guadalest: instalaci&oacute;n y mantenimiento | +QUECLIMA',
      'description' => 'Instalaci&oacute;n y mantenimiento de aire acondicionado en Guadalest y el Valle de Guadalest para viviendas de interior y uso residencial.',
      'nearby' => [
        ['Callosa d&rsquo;en Sarri&agrave;', '/es/aire-acondicionado-callosa-den-sarria/'],
        ['Benimantell', '/es/aire-acondicionado-benimantell/'],
        ['Polop', '/es/aire-acondicionado-polop/'],
      ],
    ],
    '/es/aire-acondicionado-orxeta/' => [
      'city' => 'Orxeta',
      'title' => 'Aire acondicionado en Orxeta: instalaci&oacute;n y servicio | +QUECLIMA',
      'description' => 'Servicio de instalaci&oacute;n y mantenimiento de aire acondicionado en Orxeta para viviendas residenciales en la Marina Baixa.',
      'nearby' => [
        ['Relleu', '/es/aire-acondicionado-relleu/'],
        ['Villajoyosa', '/es/aire-acondicionado-villajoyosa/'],
        ['Finestrat', '/es/aire-acondicionado-finestrat/'],
      ],
    ],
    '/es/aire-acondicionado-polop/' => [
      'city' => 'Polop',
      'title' => 'Aire acondicionado en Polop: instalaci&oacute;n y mantenimiento | +QUECLIMA',
      'description' => 'Instalaci&oacute;n y mantenimiento de aire acondicionado en Polop de la Marina para viviendas, chalets y locales en la Marina Baixa.',
      'nearby' => [
        ['La Nuc&iacute;a', '/es/aire-acondicionado-la-nucia/'],
        ['Callosa d&rsquo;en Sarri&agrave;', '/es/aire-acondicionado-callosa-den-sarria/'],
        ['Finestrat', '/es/aire-acondicionado-finestrat/'],
      ],
    ],
    '/es/aire-acondicionado-relleu/' => [
      'city' => 'Relleu',
      'title' => 'Aire acondicionado en Relleu: instalaci&oacute;n y servicio | +QUECLIMA',
      'description' => 'Servicio de instalaci&oacute;n y mantenimiento de aire acondicionado en Relleu para viviendas y casas de campo en la Marina Baixa.',
      'nearby' => [
        ['Orxeta', '/es/aire-acondicionado-orxeta/'],
        ['Sella', '/es/aire-acondicionado-sella/'],
        ['Villajoyosa', '/es/aire-acondicionado-villajoyosa/'],
      ],
    ],
    '/es/aire-acondicionado-sella/' => [
      'city' => 'Sella',
      'title' => 'Aire acondicionado en Sella: instalaci&oacute;n y mantenimiento | +QUECLIMA',
      'description' => 'Instalaci&oacute;n y mantenimiento de aire acondicionado en Sella para viviendas rurales y residenciales en la comarca de la Marina Baixa.',
      'nearby' => [
        ['Relleu', '/es/aire-acondicionado-relleu/'],
        ['Orxeta', '/es/aire-acondicionado-orxeta/'],
        ['Guadalest', '/es/aire-acondicionado-guadalest/'],
      ],
    ],
    '/es/aire-acondicionado-tarbena/' => [
      'city' => 'T&agrave;rbena',
      'title' => 'Aire acondicionado en T&agrave;rbena: instalaci&oacute;n y servicio | +QUECLIMA',
      'description' => 'Servicio de instalaci&oacute;n y mantenimiento de aire acondicionado en T&agrave;rbena para viviendas en la comarca de la Marina Alta y Marina Baixa.',
      'nearby' => [
        ['Bolulla', '/es/aire-acondicionado-bolulla/'],
        ['Callosa d&rsquo;en Sarri&agrave;', '/es/aire-acondicionado-callosa-den-sarria/'],
        ['Guadalest', '/es/aire-acondicionado-guadalest/'],
      ],
    ],
    '/es/aire-acondicionado-villajoyosa/' => [
      'city' => 'Villajoyosa',
      'title' => 'Aire acondicionado en Villajoyosa: instalaci&oacute;n y mantenimiento | +QUECLIMA',
      'description' => 'Instalaci&oacute;n, mantenimiento y reparaci&oacute;n de aire acondicionado en Villajoyosa para viviendas, apartamentos y negocios en la Costa Blanca.',
      'nearby' => [
        ['Finestrat', '/es/aire-acondicionado-finestrat/'],
        ['Benidorm', '/es/aire-acondicionado-benidorm/'],
        ['Orxeta', '/es/aire-acondicionado-orxeta/'],
      ],
    ],
  ];

  if (!isset($pages[$path])) {
    return $html;
  }

  $page = $pages[$path];
  $html = patch_snapshot_seo_meta($html, $page['title'], $page['description'], 'https://masqueclima.es' . $path);

  if (!str_contains($html, '/es/servicios/')) {
    $html = insert_p1_internal_links($html, $page);
  }

  return $html;
}

function patch_snapshot_seo_meta(string $html, string $title, string $description, string $canonical): string {
  $html = preg_replace('/<title>.*?<\/title>/is', '<title>' . $title . '</title>', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="description" content="[^"]*">/i', '<meta name="description" content="' . $description . '">', $html, 1) ?? $html;
  $html = preg_replace('/<link rel="canonical" href="[^"]*">/i', '<link rel="canonical" href="' . $canonical . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta property="og:title" content="[^"]*">/i', '<meta property="og:title" content="' . $title . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta property="og:description" content="[^"]*">/i', '<meta property="og:description" content="' . $description . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta property="og:url" content="[^"]*">/i', '<meta property="og:url" content="' . $canonical . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="twitter:title" content="[^"]*">/i', '<meta name="twitter:title" content="' . $title . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="twitter:description" content="[^"]*">/i', '<meta name="twitter:description" content="' . $description . '">', $html, 1) ?? $html;

  return $html;
}

function insert_p1_internal_links(string $html, array $page): string {
  $nearby = '';
  foreach ($page['nearby'] as [$name, $url]) {
    $nearby .= '<a class="p1-pill" href="' . $url . '">' . $name . '</a>' . "\n";
  }

  $city = $page['city'];
  $block = <<<HTML
<section class="container py-4 p1-internal-links" id="servicios-zonas">
  <style>
    .p1-internal-links{border-top:1px solid #edf2f7;border-bottom:1px solid #edf2f7;}
    .p1-internal-links .p1-box{background:#f7faff;border:1px solid #e4edf8;border-radius:14px;padding:22px;}
    .p1-internal-links h2{font-size:1.25rem;font-weight:800;color:#142033;margin-bottom:.7rem;}
    .p1-internal-links p{color:#425466;line-height:1.7;margin-bottom:1rem;}
    .p1-pill{display:inline-flex;margin:.25rem .35rem .25rem 0;border:1px solid #d5e3f2;border-radius:999px;padding:.42rem .7rem;background:#fff;color:#12324f;font-weight:700;text-decoration:none;}
    .p1-pill:hover{border-color:#0074e8;color:#0074e8;background:#fff;}
  </style>
  <div class="p1-box">
    <h2>Servicios y zonas relacionadas con {$city}</h2>
    <p>Si est&aacute;s comparando opciones, revisa nuestros servicios principales y el mapa completo de cobertura. Tambi&eacute;n atendemos zonas cercanas con la misma estructura de presupuesto, instalaci&oacute;n y postventa.</p>
    <div class="mb-2">
      <a class="p1-pill" href="/es/servicios/">Servicios de climatizaci&oacute;n</a>
      <a class="p1-pill" href="/es/zonas/">Todas las zonas</a>
      {$nearby}
    </div>
    <a class="btn btn-primary js-track" data-ev="cta_quote_p1_internal" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote" aria-controls="quoteModal">Pide presupuesto para {$city}</a>
  </div>
</section>
HTML;

  $needle = '<section class="zona" id="zona">';
  if (str_contains($html, $needle)) {
    return str_replace($needle, $block . "\n" . $needle, $html);
  }

  return str_replace('</main>', $block . "\n</main>", $html);
}


// ──────────────────────────────────────────────────
// MULTILINGUAL HUB + SERVICE PAGE RENDERING
// ──────────────────────────────────────────────────

function render_lang_hub_page(string $path, string $lang): ?string {
  $servicePage = lang_service_page_for_path($path, $lang);
  if ($servicePage !== null) {
    return render_lang_legacy_shell($servicePage, $path, render_service_detail_body($servicePage, $lang), $lang);
  }

  $page = lang_hub_page_for_path($path, $lang);
  if ($page === null) {
    return null;
  }

  $body = lang_hub_body($page, $lang);
  return render_lang_legacy_shell($page, $path, $body, $lang);
}

function lang_hub_page_for_path(string $path, string $lang): ?array {
  static $cache = [];
  if (!isset($cache[$lang])) {
    $file = __DIR__ . '/content/hubs/' . $lang . '.php';
    $loaded = is_file($file) ? require $file : [];
    $cache[$lang] = is_array($loaded) ? $loaded : [];
  }
  return $cache[$lang][$path] ?? null;
}

function lang_service_page_for_path(string $path, string $lang): ?array {
  $pages = lang_service_pages($lang);
  return $pages[$path] ?? null;
}

function lang_service_pages(string $lang): array {
  static $cache = [];
  if (isset($cache[$lang])) {
    return $cache[$lang];
  }
  $file = __DIR__ . '/content/services/' . $lang . '.php';
  $loaded = is_file($file) ? require $file : [];
  $cache[$lang] = is_array($loaded) ? $loaded : [];
  return $cache[$lang];
}

function render_lang_legacy_shell(array $page, string $path, string $body, string $lang): string {
  $homeSnapshot = snapshot_file_for_path('/' . $lang . '/');
  if ($homeSnapshot === null) {
    return render_lang_minimal_shell($page, $path, $body, $lang);
  }

  $base = file_get_contents($homeSnapshot);
  if ($base === false) {
    return render_lang_minimal_shell($page, $path, $body, $lang);
  }

  $mainOpen = '<main id="main-content">';
  $mainStart = strpos($base, $mainOpen);
  if ($mainStart === false) {
    return render_lang_minimal_shell($page, $path, $body, $lang);
  }

  $mainEnd = strpos($base, '</main>', $mainStart);
  if ($mainEnd === false) {
    return render_lang_minimal_shell($page, $path, $body, $lang);
  }

  $headAndHeader = substr($base, 0, $mainStart + strlen($mainOpen));
  $interactiveTail = legacy_es_interactive_main_tail($base, $mainStart + strlen($mainOpen), $mainEnd);
  $tail = substr($base, $mainEnd);
  $headAndHeader = patch_lang_hub_head($headAndHeader, $page, $path, $lang);
  $finalCta = final_budget_cta_html($lang);

  return $headAndHeader . "\n" . $body . "\n" . $finalCta . "\n" . $interactiveTail . "\n" . $tail;
}

function render_lang_minimal_shell(array $page, string $path, string $body, string $lang): string {
  $canonical = 'https://masqueclima.es' . $path;
  return '<!DOCTYPE html><html lang="' . e($lang) . '"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
    . '<title>' . $page['title'] . '</title><meta name="description" content="' . $page['description'] . '">'
    . '<link rel="canonical" href="' . $canonical . '"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">'
    . '<link rel="stylesheet" href="/assets/css/styles.css">' . lang_page_jsonld($path, $lang, $page) . '</head><body><main id="main-content">'
    . $body . final_budget_cta_html($lang) . '</main><script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>';
}

function patch_lang_hub_head(string $html, array $page, string $path, string $lang): string {
  $canonical = 'https://masqueclima.es' . $path;

  $html = preg_replace('/<html\b[^>]*>/i', '<html lang="' . e($lang) . '">', $html, 1) ?? $html;
  $html = preg_replace('/<title>.*?<\/title>/is', '<title>' . $page['title'] . '</title>', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="description" content="[^"]*">/i', '<meta name="description" content="' . $page['description'] . '">', $html, 1) ?? $html;
  $html = preg_replace('/<link rel="canonical" href="[^"]*">/i', '<link rel="canonical" href="' . $canonical . '">', $html, 1) ?? $html;
  $html = preg_replace('/<!-- Hreflang -->\s*(?:<link rel="alternate"[^>]+>\s*)+/i', '', $html, 1) ?? $html;
  $html = preg_replace('/<meta property="og:title" content="[^"]*">/i', '<meta property="og:title" content="' . $page['title'] . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta property="og:description" content="[^"]*">/i', '<meta property="og:description" content="' . $page['description'] . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta property="og:url" content="[^"]*">/i', '<meta property="og:url" content="' . $canonical . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="twitter:title" content="[^"]*">/i', '<meta name="twitter:title" content="' . $page['title'] . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="twitter:description" content="[^"]*">/i', '<meta name="twitter:description" content="' . $page['description'] . '">', $html, 1) ?? $html;
  $html = patch_es_hub_hero_preload($html, $page);
  $html = str_replace('</head>', lang_page_jsonld($path, $lang, $page) . "\n</head>", $html);

  return $html;
}

function lang_page_jsonld(string $path, string $lang, array $page): string {
  $base = 'https://masqueclima.es';
  $breadcrumbName = (string) ($page['breadcrumb'] ?? 'Page');
  $homeLabel = match ($lang) {
    'en'    => 'Home',
    'de'    => 'Startseite',
    'nl'    => 'Home',
    'ru'    => 'Главная',
    'no'    => 'Hjem',
    default => 'Inicio',
  };
  $servicesHubUrl = localized_hub_url($lang, 'services');
  $servicesLabel = match ($lang) {
    'en'    => 'Services',
    'de'    => 'Dienstleistungen',
    'nl'    => 'Diensten',
    'ru'    => 'Услуги',
    'no'    => 'Tjenester',
    default => 'Servicios',
  };

  $itemList = [[
    '@type' => 'ListItem',
    'position' => 1,
    'name' => $homeLabel,
    'item' => $base . '/' . $lang . '/',
  ]];

  if ($servicesHubUrl !== null && str_starts_with($path, $servicesHubUrl) && $path !== $servicesHubUrl) {
    $itemList[] = [
      '@type' => 'ListItem',
      'position' => 2,
      'name' => $servicesLabel,
      'item' => $base . $servicesHubUrl,
    ];
  }

  $itemList[] = [
    '@type' => 'ListItem',
    'position' => count($itemList) + 1,
    'name' => html_entity_decode($breadcrumbName, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
    'item' => $base . $path,
  ];

  $schemas = [[
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => $itemList,
  ]];

  if (!empty($page['service_type'])) {
    $schemas[] = [
      '@context' => 'https://schema.org',
      '@type' => 'Service',
      'name' => html_entity_decode((string) $page['service_type'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
      'description' => html_entity_decode((string) $page['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
      'url' => $base . $path,
      'provider' => [
        '@type' => 'HVACBusiness',
        'name' => '+QUECLIMA',
        'telephone' => '+34 613 02 66 00',
        'url' => $base . '/es/',
      ],
      'areaServed' => ['Benidorm', 'Altea', 'Calpe', 'Finestrat', 'La Nucia', 'Marina Baixa', 'Alicante'],
      'serviceType' => html_entity_decode((string) $page['service_type'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
    ];
  }

  if (!empty($page['faq']) && is_array($page['faq'])) {
    $faqItems = [];
    foreach ($page['faq'] as $item) {
      if (!empty($item['q']) && !empty($item['a'])) {
        $faqItems[] = [
          '@type' => 'Question',
          'name' => html_entity_decode((string) $item['q'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
          'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => html_entity_decode((string) $item['a'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
          ],
        ];
      }
    }
    if (!empty($faqItems)) {
      $schemas[] = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqItems,
      ];
    }
  }

  return '<script type="application/ld+json">'
    . json_encode($schemas, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    . '</script>';
}

function lang_hub_body(array $page, string $lang): string {
  return match ($page['hub_type'] ?? '') {
    'services' => lang_hub_services_body($page, $lang),
    'areas'    => lang_hub_areas_body($page, $lang),
    'guides'   => lang_hub_guides_body($page, $lang),
    default    => '',
  };
}

function lang_hub_services_body(array $page, string $lang): string {
  $h1 = $page['h1'] ?? '';
  $introText = $page['hub_intro'] ?? '';
  $kicker = $page['hub_kicker'] ?? '';
  $h2 = $page['hub_h2'] ?? '';
  $p = $page['hub_p'] ?? '';
  $zonesKicker = $page['hub_zones_kicker'] ?? '';
  $zonesH2 = $page['hub_zones_h2'] ?? '';
  $zonesP = $page['hub_zones_p'] ?? '';
  $zonesAll = $page['hub_zones_all'] ?? '';
  $styles = hub_styles();
  $intro = hub_intro($h1, $introText, hub_visual_options($page));
  $serviceCards = lang_service_cards_html($lang);
  $miniMap = render_partial('zone_visual', ['variant' => 'mini']);
  $localityPills = lang_locality_pills_html($lang);
  $zonesHubUrl = e(localized_hub_url($lang, 'zones') ?? '#');

  return <<<HTML
{$styles}
{$intro}
<section class="hub-section" aria-labelledby="lang-services-main">
  <div class="container">
    <p class="hub-kicker">{$kicker}</p>
    <h2 class="section-title" id="lang-services-main">{$h2}</h2>
    <p class="hub-muted">{$p}</p>
    {$serviceCards}
  </div>
</section>
<section class="hub-section alt" aria-labelledby="lang-services-zones">
  <div class="container">
    <p class="hub-kicker">{$zonesKicker}</p>
    <h2 class="section-title" id="lang-services-zones">{$zonesH2}</h2>
    <div class="service-zones-panel">
      <div class="service-zones-copy">
        <p class="hub-muted">{$zonesP}</p>
        <div class="hub-links">
          {$localityPills}
          <a class="hub-pill" href="{$zonesHubUrl}">{$zonesAll}</a>
        </div>
      </div>
      {$miniMap}
    </div>
  </div>
</section>
HTML;
}

function lang_hub_areas_body(array $page, string $lang): string {
  $h1 = $page['h1'] ?? '';
  $introText = $page['hub_intro'] ?? '';
  $kicker = $page['hub_kicker'] ?? '';
  $h2 = $page['hub_h2'] ?? '';
  $p = $page['hub_p'] ?? '';
  $contextKicker = $page['hub_context_kicker'] ?? '';
  $contextH2 = $page['hub_context_h2'] ?? '';
  $contextP = $page['hub_context_p'] ?? '';
  $ctaLabel = $page['hub_cta'] ?? 'Request a quote';
  $selectP = $page['hub_select_p'] ?? '';
  $styles = hub_styles();
  $intro = hub_intro($h1, $introText, hub_visual_options($page));
  $zoneMap = render_partial('zone_visual');
  $allPills = lang_all_locality_pills_html($lang);

  return <<<HTML
{$styles}
{$intro}
<section class="hub-section" aria-labelledby="lang-areas-list">
  <div class="container">
    <p class="hub-kicker">{$kicker}</p>
    <h2 class="section-title" id="lang-areas-list">{$h2}</h2>
    <p class="hub-muted">{$p}</p>
    <div class="zone-layout">
      {$zoneMap}
      <div class="zone-list-card">
        <p class="hub-muted">{$selectP}</p>
        <div class="hub-links zone-chip-grid">
          {$allPills}
        </div>
        <div class="hub-cta">
          <a class="btn btn-primary js-track" data-ev="cta_quote_areas" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote">{$ctaLabel}</a>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="hub-section alt" aria-labelledby="lang-areas-context">
  <div class="container">
    <p class="hub-kicker">{$contextKicker}</p>
    <h2 class="section-title" id="lang-areas-context">{$contextH2}</h2>
    <p class="hub-muted">{$contextP}</p>
  </div>
</section>
HTML;
}

function lang_hub_guides_body(array $page, string $lang): string {
  $h1 = $page['h1'] ?? '';
  $introText = $page['hub_intro'] ?? '';
  $kicker = $page['hub_kicker'] ?? '';
  $h2 = $page['hub_h2'] ?? '';
  $p = $page['hub_p'] ?? '';
  $styles = hub_styles();
  $intro = hub_intro($h1, $introText, hub_visual_options($page));

  return <<<HTML
{$styles}
{$intro}
<section class="hub-section" aria-labelledby="lang-guides-list">
  <div class="container">
    <p class="hub-kicker">{$kicker}</p>
    <h2 class="section-title" id="lang-guides-list">{$h2}</h2>
    <p class="hub-muted">{$p}</p>
  </div>
</section>
HTML;
}

function lang_service_cards_html(string $lang): string {
  $services = lang_service_pages($lang);
  if (empty($services)) {
    return '';
  }
  $html = '<div class="service-cards-grid">' . "\n";
  foreach ($services as $path => $s) {
    $title = e($s['h1'] ?? $s['title'] ?? '');
    $desc  = e($s['subtitle'] ?? $s['description'] ?? '');
    $url   = e($path);
    $html .= '  <a class="service-card" href="' . $url . '">'
      . '<strong>' . $title . '</strong>'
      . '<span>' . $desc . '</span>'
      . '</a>' . "\n";
  }
  $html .= '</div>';
  return $html;
}

function lang_locality_pills_html(string $lang): string {
  $urlPrefix = match ($lang) {
    'en'    => '/en/air-conditioning-',
    'de'    => '/de/klimaanlage-',
    'nl'    => '/nl/airco-',
    'ru'    => '/ru/konditsioner-',
    'no'    => '/no/aircondition-',
    default => '/es/aire-acondicionado-',
  };
  $featured = ['benidorm', 'altea', 'calpe', 'finestrat', 'la-nucia'];
  $labels = [
    'benidorm'  => 'Benidorm',
    'altea'     => 'Altea',
    'calpe'     => 'Calpe',
    'finestrat' => 'Finestrat',
    'la-nucia'  => 'La Nuc&iacute;a',
  ];
  $html = '';
  foreach ($featured as $slug) {
    $url   = e($urlPrefix . $slug . '/');
    $label = $labels[$slug];
    $html .= '<a class="hub-pill" href="' . $url . '">' . $label . '</a>' . "\n";
  }
  return $html;
}

function lang_all_locality_pills_html(string $lang): string {
  $urlPrefix = match ($lang) {
    'en'    => '/en/air-conditioning-',
    'de'    => '/de/klimaanlage-',
    'nl'    => '/nl/airco-',
    'ru'    => '/ru/konditsioner-',
    'no'    => '/no/aircondition-',
    default => '/es/aire-acondicionado-',
  };
  $zones = [
    ['Albir',                          'albir'],
    ['Alfaz del Pi',                   'alfaz-del-pi'],
    ['Altea',                          'altea'],
    ['Beniard&agrave;',                'beniarda'],
    ['Benidorm',                       'benidorm'],
    ['Benifato',                       'benifato'],
    ['Benimantell',                    'benimantell'],
    ['Bolulla',                        'bolulla'],
    ['Callosa d&rsquo;en Sarri&agrave;', 'callosa-den-sarria'],
    ['Calpe',                          'calpe'],
    ['Confrides',                      'confrides'],
    ['Finestrat',                      'finestrat'],
    ['Guadalest',                      'guadalest'],
    ['La Nuc&iacute;a',                'la-nucia'],
    ['Orxeta',                         'orxeta'],
    ['Polop',                          'polop'],
    ['Relleu',                         'relleu'],
    ['Sella',                          'sella'],
    ['T&agrave;rbena',                 'tarbena'],
    ['Villajoyosa',                    'villajoyosa'],
  ];
  $html = '';
  foreach ($zones as [$name, $slug]) {
    $url   = e($urlPrefix . $slug . '/');
    $html .= '<a class="hub-pill" href="' . $url . '">' . $name . '</a>' . "\n";
  }
  return $html;
}