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

  $html = patch_es_p1_location_page($html, $path, $lang);

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
  if ($lang !== 'es' || str_contains($html, 'footer-guides-link')) {
    return $html;
  }

  return preg_replace(
    '/(<footer class="bg-dark text-white py-4">[\s\S]*?<div class="container text-center">)/',
    '$1' . "\n" . '    <p class="mb-1 footer-guides-link"><a class="text-white" href="/es/blog/">Guías</a></p>',
    $html,
    1
  ) ?? $html;
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
      'itemListElement' => [[
        '@type' => 'ListItem',
        'position' => 1,
        'name' => 'Home',
        'item' => $url,
      ]],
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
  $pages = [
    '/es/servicios/' => [
      'slug' => 'servicios',
      'title' => 'Servicios de climatizaci&oacute;n en Benidorm y Marina Baixa | +QUECLIMA',
      'description' => 'Instalaci&oacute;n, mantenimiento y reparaci&oacute;n de aire acondicionado, bomba de calor y energ&iacute;a solar en Benidorm, Marina Baixa y Alicante.',
      'h1' => 'Servicios de climatizaci&oacute;n en Benidorm y Marina Baixa',
      'breadcrumb' => 'Servicios',
    ],
    '/es/zonas/' => [
      'slug' => 'zonas',
      'title' => 'Servicio de climatizaci&oacute;n por zonas en Alicante | +QUECLIMA',
      'description' => 'Cobertura local de climatizaci&oacute;n en Benidorm, Altea, Calpe, Finestrat, La Nuc&iacute;a y otras zonas de Alicante.',
      'h1' => 'Servicio de climatizaci&oacute;n por zonas en Alicante',
      'breadcrumb' => 'Zonas',
    ],
    '/es/blog/' => [
      'slug' => 'blog',
      'title' => 'Gu&iacute;as de climatizaci&oacute;n y aire acondicionado | +QUECLIMA',
      'description' => 'Gu&iacute;as pr&aacute;cticas sobre aire acondicionado, potencia, mantenimiento, ahorro y sistemas de climatizaci&oacute;n en la Costa Blanca.',
      'h1' => 'Gu&iacute;as de climatizaci&oacute;n y aire acondicionado',
      'breadcrumb' => 'Gu&iacute;as',
    ],
  ];

  if (!isset($pages[$path])) {
    return null;
  }

  $page = $pages[$path];
  $body = match ($page['slug']) {
    'servicios' => hub_services_body(),
    'zonas' => hub_zones_body(),
    'blog' => hub_blog_body(),
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

  return $headAndHeader . "\n" . $body . "\n" . $interactiveTail . "\n" . $tail;
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

function render_es_minimal_shell(array $page, string $path, string $body): string {
  $canonical = 'https://masqueclima.es' . $path;
  return '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">' .
    '<title>' . $page['title'] . '</title><meta name="description" content="' . $page['description'] . '">' .
    '<link rel="canonical" href="' . $canonical . '"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">' .
    '<link rel="stylesheet" href="/assets/css/styles.css">' . es_hub_jsonld($path, $page['breadcrumb']) . '</head><body><main id="main-content">' .
    $body . '</main><script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>';
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
  $html = str_replace('</head>', es_hub_jsonld($path, $page['breadcrumb']) . "\n</head>", $html);

  return $html;
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

function hub_intro(string $h1, string $text): string {
  return <<<HTML
<section class="hero hub-hero" id="inicio">
  <div class="hero-bg">
    <img class="hero-img" src="/assets/img/hero1.webp" alt="+QUECLIMA climatizaci&oacute;n en Alicante" width="1920" height="1080" fetchpriority="high" decoding="async">
  </div>
  <div class="container">
    <h1>{$h1}</h1>
    <p>{$text}</p>
    <div class="cta-group">
      <a class="cta-button js-track" data-ev="cta_quote_hub" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote" aria-controls="quoteModal">Pide presupuesto sin compromiso</a>
      <a class="cta-button cta-whatsapp js-track" data-ev="cta_whatsapp_hub" href="https://wa.me/34613026600" target="_blank" rel="noopener" aria-label="Escr&iacute;benos por WhatsApp">Escr&iacute;benos por WhatsApp</a>
    </div>
  </div>
</section>
HTML;
}

function hub_styles(): string {
  return <<<HTML
<style>
  .hub-hero{min-height:58svh;}
  .hub-section{padding:4.5rem 0;background:#fff;}
  .hub-section.alt{background:#f7faff;}
  .hub-kicker{color:#0074e8;font-weight:800;text-transform:uppercase;font-size:.78rem;letter-spacing:.08em;margin-bottom:.6rem;}
  .hub-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:18px;margin-top:1.5rem;}
  .hub-card{border:1px solid #e7edf5;border-radius:14px;background:#fff;padding:22px;box-shadow:0 5px 18px rgba(15,23,42,.06);}
  .hub-card h2,.hub-card h3{font-size:1.12rem;font-weight:800;margin:0 0 .7rem;color:#142033;}
  .hub-card p{color:#425466;line-height:1.65;margin-bottom:1rem;}
  .hub-links{display:flex;flex-wrap:wrap;gap:.55rem;margin-top:1rem;}
  .hub-pill{border:1px solid #dbe7f5;border-radius:999px;padding:.45rem .72rem;text-decoration:none;color:#12324f;background:#fff;font-weight:700;font-size:.92rem;}
  .hub-pill:hover{border-color:#0074e8;color:#0074e8;background:#f4f9ff;}
  .hub-cta{margin-top:1.4rem;display:flex;flex-wrap:wrap;gap:.75rem;}
  .hub-muted{color:#5c6b7c;line-height:1.75;}
  .blog-soon{display:inline-flex;border-radius:999px;background:#eef6ff;color:#07549e;font-weight:800;font-size:.82rem;padding:.28rem .6rem;margin-bottom:.75rem;}
  @media (max-width:700px){.hub-section{padding:3rem 0}.hub-hero{min-height:64svh}}
</style>
HTML;
}

function hub_services_body(): string {
  $intro = hub_intro(
    'Servicios de climatizaci&oacute;n en Benidorm y Marina Baixa',
    'Instalamos, mantenemos y reparamos sistemas de aire acondicionado, calefacci&oacute;n y energ&iacute;a para viviendas, apartamentos tur&iacute;sticos, comunidades y negocios.'
  );
  $styles = hub_styles();

  return <<<HTML
{$styles}
{$intro}
<section class="hub-section" aria-labelledby="servicios-principales">
  <div class="container">
    <p class="hub-kicker">Servicios</p>
    <h2 class="section-title" id="servicios-principales">Soluciones principales</h2>
    <p class="hub-muted">Trabajamos con equipos split, multisplit, sistemas por conductos y bomba de calor. Antes de instalar revisamos potencia, ubicaci&oacute;n, drenajes, ruido, consumo y mantenimiento futuro.</p>
    <div class="hub-grid">
      <article class="hub-card">
        <h3>Instalaci&oacute;n de aire acondicionado</h3>
        <p>Instalamos equipos split, multisplit y conductos con visita previa, c&aacute;lculo de potencia y puesta en marcha cuidada.</p>
        <a class="btn btn-primary js-track" data-ev="cta_quote_service_install" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote">Pedir presupuesto</a>
      </article>
      <article class="hub-card">
        <h3>Mantenimiento de climatizaci&oacute;n</h3>
        <p>Revisamos filtros, bater&iacute;as, desag&uuml;es y unidades exteriores para alargar la vida del equipo y reducir consumo.</p>
        <a class="btn btn-primary js-track" data-ev="cta_quote_service_maintenance" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote">Solicitar revisi&oacute;n</a>
      </article>
      <article class="hub-card">
        <h3>Reparaci&oacute;n de aire acondicionado</h3>
        <p>Atendemos equipos que no enfr&iacute;an, hacen ruido, pierden agua o muestran errores, con diagn&oacute;stico claro antes de reparar.</p>
        <a class="btn btn-primary js-track" data-ev="cta_quote_service_repair" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote">Consultar aver&iacute;a</a>
      </article>
      <article class="hub-card">
        <h3>Calefacci&oacute;n y bomba de calor</h3>
        <p>Soluciones eficientes para invierno y entretiempo, con equipos silenciosos y control por zonas cuando la vivienda lo necesita.</p>
        <a class="btn btn-primary js-track" data-ev="cta_quote_service_heatpump" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote">Valorar opciones</a>
      </article>
      <article class="hub-card">
        <h3>Energ&iacute;a solar t&eacute;rmica</h3>
        <p>Valoramos apoyo solar para agua caliente y eficiencia energ&eacute;tica cuando encaja con el uso de la vivienda o el negocio.</p>
        <a class="btn btn-primary js-track" data-ev="cta_quote_service_solar" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote">Pedir estudio</a>
      </article>
    </div>
  </div>
</section>
<section class="hub-section alt" aria-labelledby="zonas-destacadas">
  <div class="container">
    <p class="hub-kicker">Zonas destacadas</p>
    <h2 class="section-title" id="zonas-destacadas">Servicio local en la Marina Baixa</h2>
    <p class="hub-muted">Trabajamos a diario en Benidorm, Altea, Calpe, Finestrat y La Nuc&iacute;a, y tambi&eacute;n nos desplazamos a otras localidades cercanas.</p>
    <div class="hub-links">
      <a class="hub-pill" href="/es/aire-acondicionado-benidorm/">Aire acondicionado en Benidorm</a>
      <a class="hub-pill" href="/es/aire-acondicionado-altea/">Aire acondicionado en Altea</a>
      <a class="hub-pill" href="/es/aire-acondicionado-calpe/">Aire acondicionado en Calpe</a>
      <a class="hub-pill" href="/es/aire-acondicionado-finestrat/">Aire acondicionado en Finestrat</a>
      <a class="hub-pill" href="/es/aire-acondicionado-la-nucia/">Aire acondicionado en La Nuc&iacute;a</a>
      <a class="hub-pill" href="/es/zonas/">Ver todas las zonas</a>
    </div>
  </div>
</section>
HTML;
}

function hub_zones_body(): string {
  $intro = hub_intro(
    'Servicio de climatizaci&oacute;n por zonas en Alicante',
    'Trabajamos desde Benidorm para la Marina Baixa, Costa Blanca norte y provincia de Alicante, con desplazamiento r&aacute;pido y asesoramiento cercano.'
  );
  $styles = hub_styles();
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
    <p class="hub-muted">Estas son algunas de las zonas donde realizamos instalaciones, mantenimiento y reparaciones de climatizaci&oacute;n.</p>
    <div class="hub-links">
      {$items}
    </div>
    <div class="hub-cta">
      <a class="btn btn-primary js-track" data-ev="cta_quote_zones" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote">Pedir presupuesto</a>
      <a class="btn btn-outline-primary" href="/es/servicios/">Ver servicios</a>
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

function hub_blog_body(): string {
  $intro = hub_intro(
    'Gu&iacute;as de climatizaci&oacute;n y aire acondicionado',
    'Consejos pr&aacute;cticos para elegir, mantener y aprovechar mejor tu sistema de climatizaci&oacute;n en la Costa Blanca.'
  );
  $styles = hub_styles();

  return <<<HTML
{$styles}
{$intro}
<section class="hub-section" aria-labelledby="blog-plan">
  <div class="container">
    <p class="hub-kicker">Gu&iacute;as</p>
    <h2 class="section-title" id="blog-plan">Consejos que estamos preparando</h2>
    <p class="hub-muted">Estamos preparando gu&iacute;as &uacute;tiles sobre instalaci&oacute;n, mantenimiento y ahorro energ&eacute;tico. Mientras tanto, puedes consultarnos directamente y te orientamos seg&uacute;n tu vivienda o negocio.</p>
    <div class="hub-grid">
      <article class="hub-card">
        <h3>Cu&aacute;nto cuesta instalar aire acondicionado en Alicante</h3>
        <p>Factores que influyen en el precio: tipo de equipo, distancia de instalaci&oacute;n, potencia y caracter&iacute;sticas de la vivienda.</p>
      </article>
      <article class="hub-card">
        <h3>Qu&eacute; potencia de aire acondicionado necesito</h3>
        <p>Una orientaci&oacute;n sencilla sobre frigor&iacute;as, metros cuadrados, aislamiento, orientaci&oacute;n y uso real de cada estancia.</p>
      </article>
      <article class="hub-card">
        <h3>Mantenimiento de aire acondicionado en zonas de costa</h3>
        <p>Recomendaciones para salitre, filtros, bater&iacute;as, drenajes y revisiones antes de temporada alta.</p>
      </article>
    </div>
    <div class="hub-cta">
      <a class="btn btn-primary js-track" data-ev="cta_quote_blog" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote">Resolver una duda</a>
      <a class="btn btn-outline-primary" href="/es/servicios/">Ver servicios</a>
      <a class="btn btn-outline-primary" href="/es/zonas/">Ver zonas</a>
    </div>
  </div>
</section>
HTML;
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
