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
  $html = patch_snapshot_home_context_links($html, $path, $lang);

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
  $homeLabel = e(t('nav.home', 'Inicio'));
  $faqLabel = e(t('nav.faq', 'FAQ'));

  return '<p class="mb-1 footer-main-links"><a class="text-white" href="' . $home . '">' . $homeLabel . '</a> | <a class="text-white" href="' . $faq . '">' . $faqLabel . '</a></p>';
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
      'title' => 'Gu&iacute;as de climatizaci&oacute;n, aerotermia y aire acondicionado | +QUECLIMA',
      'description' => 'Gu&iacute;as pr&aacute;cticas sobre climatizaci&oacute;n, aerotermia, bomba de calor, mantenimiento, consumo e instalaci&oacute;n en la Costa Blanca.',
      'h1' => 'Gu&iacute;as de climatizaci&oacute;n, aerotermia y aire acondicionado',
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
  if ($lang !== 'es') {
    return '';
  }

  return <<<HTML
<style>
  .budget-panel.budget-panel--final{background:#f7f9fc;padding:2.75rem 0;}
  .budget-panel.budget-panel--final .box{background:rgba(255,255,255,.96);backdrop-filter:saturate(180%) blur(10px);-webkit-backdrop-filter:saturate(180%) blur(10px);border:1px solid rgba(0,0,0,.06);border-radius:16px;box-shadow:0 12px 34px rgba(0,0,0,.10);padding:2.5rem 2.25rem;}
  .budget-panel.budget-panel--final .grid{display:grid;grid-template-columns:1.35fr 1fr;gap:2.25rem;align-items:center;}
  .budget-panel.budget-panel--final h2{margin:0 0 .6rem;font-weight:900;letter-spacing:-.02em;color:var(--main,#111);}
  .budget-panel.budget-panel--final p{margin:0;color:#3b4a5a;opacity:.95;line-height:1.75;}
  .budget-panel.budget-panel--final .bullets{margin:1.1rem 0 0;padding:0;list-style:none;color:#425264;}
  .budget-panel.budget-panel--final .bullets li{margin:.35rem 0;display:flex;gap:.6rem;align-items:flex-start;}
  .budget-panel.budget-panel--final .bullets .dot{width:.5rem;height:.5rem;margin-top:.55rem;border-radius:50%;background:#c9d7e6;flex:0 0 auto;}
  .budget-panel.budget-panel--final .cta-stack{display:flex;flex-direction:column;gap:.75rem;}
  .budget-panel.budget-panel--final .cta-btn2{display:flex;align-items:center;justify-content:center;gap:.6rem;width:100%;padding:.9rem 1.05rem;border-radius:10px;font-weight:700;text-decoration:none!important;background:transparent;border:2px solid;box-shadow:none;transition:transform .15s ease,background-color .15s ease,color .15s ease,border-color .15s ease;}
  .budget-panel.budget-panel--final .cta-btn2 svg{width:20px;height:20px;flex:0 0 auto;}
  .budget-panel.budget-panel--final .cta-outline-blue{color:var(--accent,#0074e8);border-color:var(--accent,#0074e8);}
  .budget-panel.budget-panel--final .cta-outline-blue:hover{background:rgba(0,116,232,.06);transform:translateY(-1px);}
  .budget-panel.budget-panel--final .cta-outline-dark{color:#1b2a3a;border-color:#d7e0ea;}
  .budget-panel.budget-panel--final .cta-outline-dark:hover{background:rgba(24,32,45,.045);transform:translateY(-1px);}
  .budget-panel.budget-panel--final .cta-outline-wa{color:var(--wa,#25d366);border-color:var(--wa,#25d366);}
  .budget-panel.budget-panel--final .cta-outline-wa:hover{background:rgba(37,211,102,.08);transform:translateY(-1px);}
  .budget-panel.budget-panel--final .cta-note{margin-top:.9rem;font-size:.9rem;color:#667789;text-align:center;}
  @media (max-width:991.98px){.budget-panel.budget-panel--final{padding:3.25rem 0}.budget-panel.budget-panel--final .box{padding:2rem 1.35rem}.budget-panel.budget-panel--final .grid{grid-template-columns:1fr;gap:1.5rem}}
</style>
<section class="budget-panel budget-panel--final" id="presupuesto">
  <div class="container">
    <div class="box">
      <div class="grid">
        <div>
          <h2>Solicita tu presupuesto</h2>
          <p>Instalaci&oacute;n, mantenimiento o reparaci&oacute;n. Te asesoramos con una propuesta clara y honesta.</p>
          <ul class="bullets" aria-hidden="true">
            <li><span class="dot"></span><span>Respuesta r&aacute;pida y sin compromiso</span></li>
            <li><span class="dot"></span><span>Servicio en Benidorm y Marina Baixa</span></li>
            <li><span class="dot"></span><span>Trabajo profesional con garant&iacute;a real</span></li>
          </ul>
        </div>
        <div>
          <div class="cta-stack">
            <a class="cta-btn2 cta-outline-blue js-track" data-ev="cta_quote_final" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote" aria-controls="quoteModal">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="5" width="18" height="14" rx="2" ry="2"></rect>
                <path d="m3 7 9 6 9-6"></path>
              </svg>
              <span>Enviar formulario</span>
            </a>
            <a class="cta-btn2 cta-outline-dark js-track" data-ev="cta_call_final" href="tel:+34613026600" aria-label="Llamar por tel&eacute;fono">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.12.86.31 1.7.56 2.5a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.58-1.08a2 2 0 0 1 2.11-.45c.8.25 1.64.44 2.5.56A2 2 0 0 1 22 16.92z"></path>
              </svg>
              <span>Llamar +34 613 02 66 00</span>
            </a>
            <a class="cta-btn2 cta-outline-wa js-track" data-ev="cta_whatsapp_final" href="https://wa.me/34613026600" target="_blank" rel="noopener" aria-label="Escr&iacute;benos por WhatsApp">
              <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M.057 24l1.687-6.163a11.867 11.867 0 1 1 4.279 4.29L.057 24Zm6.597-3.807.39.232a9.868 9.868 0 1 0-3.63-3.63l.232.39-.99 3.62 3.998-1.612ZM8.85 7.845c-.176-.392-.362-.4-.53-.407-.137-.006-.294-.006-.451-.006a.868.868 0 0 0-.626.294c-.215.23-.827.807-.827 1.968s.846 2.282.964 2.44c.118.157 1.63 2.618 4.02 3.563.562.227 1 .363 1.341.465.563.179 1.075.153 1.48.093.451-.068 1.39-.567 1.586-1.115.196-.548.196-1.018.137-1.115-.059-.098-.215-.157-.451-.274-.235-.118-1.39-.685-1.604-.763-.215-.078-.373-.117-.53.118-.157.235-.607.763-.744.92-.137.157-.274.176-.51.059-.235-.118-.993-.366-1.89-1.17-.699-.622-1.172-1.39-1.309-1.625-.137-.235-.014-.362.104-.48.107-.106.235-.274.353-.411.117-.137.156-.235.235-.392.078-.157.039-.294-.02-.411-.059-.118-.51-1.246-.716-1.706Z"></path>
              </svg>
              <span>Escr&iacute;benos por WhatsApp</span>
            </a>
          </div>
          <div class="cta-note">Elige c&oacute;mo prefieres que te contactemos. Respuesta r&aacute;pida.</div>
        </div>
      </div>
    </div>
  </div>
</section>
HTML;
}

function render_es_minimal_shell(array $page, string $path, string $body): string {
  $canonical = 'https://masqueclima.es' . $path;
  return '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">' .
    '<title>' . $page['title'] . '</title><meta name="description" content="' . $page['description'] . '">' .
    '<link rel="canonical" href="' . $canonical . '"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">' .
    '<link rel="stylesheet" href="/assets/css/styles.css">' . es_hub_jsonld($path, $page['breadcrumb']) . '</head><body><main id="main-content">' .
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
<section class="hero hub-hero hero--compact" id="inicio">
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
  .hero.hub-hero.hero--compact{min-height:380px;padding:6.75rem 0 3rem;display:flex;align-items:center;text-align:center;}
  .hero.hub-hero.hero--compact .container{max-width:960px;margin:0 auto;}
  .hero.hub-hero.hero--compact h1{font-size:3rem;line-height:1.12;margin:0 auto .85rem;}
  .hero.hub-hero.hero--compact p{max-width:760px;margin:0 auto;color:#eef5fb;line-height:1.65;font-size:1.08rem;}
  .hero.hub-hero.hero--compact .cta-group{margin-top:1.25rem;}
  .hub-section{padding:3.5rem 0;background:#fff;}
  .hub-section.alt{background:#f7faff;}
  .hub-section .container>.section-title,.hub-section .container>.hub-kicker{text-align:center;}
  .hub-section .container>.hub-muted{max-width:860px;margin-left:auto;margin-right:auto;text-align:center;}
  .hub-kicker{color:#0074e8;font-weight:800;text-transform:uppercase;font-size:.78rem;letter-spacing:.08em;margin-bottom:.6rem;}
  .hub-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:18px;margin-top:1.5rem;align-items:stretch;}
  .hub-grid.services-grid{grid-template-columns:repeat(5,minmax(0,1fr));}
  .hub-grid.guides-grid{grid-template-columns:repeat(3,minmax(0,1fr));}
  .hub-card{border:1px solid #e7edf5;border-radius:14px;background:#fff;padding:22px;box-shadow:0 5px 18px rgba(15,23,42,.06);display:flex;flex-direction:column;height:100%;transition:transform .16s ease,box-shadow .16s ease,border-color .16s ease;}
  .hub-card:hover{transform:translateY(-3px);box-shadow:0 12px 26px rgba(15,23,42,.09);border-color:#d6e6f6;}
  .hub-card h2,.hub-card h3{font-size:1.12rem;font-weight:800;margin:0 0 .7rem;color:#142033;}
  .hub-card p{color:#425466;line-height:1.65;margin-bottom:1rem;flex:1;}
  .hub-card .btn{align-self:center;margin-top:auto;min-width:160px;}
  .hub-links{display:flex;flex-wrap:wrap;justify-content:center;gap:.55rem;margin-top:1rem;}
  .hub-pill{border:1px solid #dbe7f5;border-radius:999px;padding:.45rem .72rem;text-decoration:none;color:#12324f;background:#fff;font-weight:700;font-size:.92rem;}
  .hub-pill:hover{border-color:#0074e8;color:#0074e8;background:#f4f9ff;}
  .hub-cta{margin-top:1.4rem;display:flex;flex-wrap:wrap;justify-content:center;gap:.75rem;}
  .hub-muted{color:#5c6b7c;line-height:1.75;}
  .service-zones-panel,.zone-layout{display:grid;grid-template-columns:1.05fr .95fr;gap:28px;align-items:center;margin-top:1.75rem;}
  .service-zones-copy,.mini-map-card,.zone-map-card,.zone-list-card{background:#fff;border:1px solid #e4edf8;border-radius:16px;box-shadow:0 8px 24px rgba(15,23,42,.06);padding:24px;}
  .mini-map-card,.zone-map-card{background:linear-gradient(180deg,#f8fbff 0%,#eef7ff 100%);}
  .marina-map{width:100%;height:auto;display:block;}
  .marina-map .sea{fill:#dff3ff;}
  .marina-map .coast{fill:none;stroke:#7db6e8;stroke-width:3;stroke-linecap:round;}
  .marina-map .route{fill:none;stroke:#c7d9ec;stroke-width:2;stroke-dasharray:5 7;stroke-linecap:round;}
  .marina-map .zone-shape{fill:#eaf4ff;stroke:#0074e8;stroke-width:1.5;transition:fill .16s ease,stroke .16s ease,transform .16s ease;}
  .marina-map .map-label{font-size:12px;font-weight:800;fill:#12324f;opacity:.82;pointer-events:none;transition:opacity .16s ease,fill .16s ease;}
  .marina-map .map-zone:hover .zone-shape,.marina-map .map-zone:focus .zone-shape{fill:#bfe0ff;stroke:#005fc0;}
  .marina-map .map-zone:hover .map-label,.marina-map .map-zone:focus .map-label{fill:#005fc0;opacity:1;}
  .zone-list-card .hub-muted{text-align:center;}
  @media (max-width:1100px){.hub-grid.services-grid{grid-template-columns:repeat(3,minmax(0,1fr));}.hub-grid.guides-grid{grid-template-columns:repeat(2,minmax(0,1fr));}}
  @media (max-width:900px){.service-zones-panel,.zone-layout{grid-template-columns:1fr}.mini-map-card{order:-1}}
  @media (max-width:700px){.hero.hub-hero.hero--compact{min-height:330px;padding:6rem 0 2.4rem}.hero.hub-hero.hero--compact h1{font-size:2rem}.hero.hub-hero.hero--compact p{font-size:1rem}.hub-section{padding:2.6rem 0}.hub-grid.services-grid,.hub-grid.guides-grid{grid-template-columns:1fr}.service-zones-copy,.mini-map-card,.zone-map-card,.zone-list-card{padding:18px}}
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
    <h2 class="section-title" id="servicios-principales">Soluciones de climatizaci&oacute;n</h2>
    <p class="hub-muted">Trabajamos con equipos split, multisplit, conductos y bomba de calor. Antes de instalar revisamos la vivienda o el local para recomendar una soluci&oacute;n eficiente y ajustada al uso real.</p>
    <div class="hub-grid services-grid">
      <article class="hub-card">
        <h3>Instalaci&oacute;n de aire acondicionado</h3>
        <p>Instalamos equipos split, multisplit y conductos con visita previa, c&aacute;lculo de potencia y puesta en marcha cuidada.</p>
        <a class="btn btn-primary js-track" data-ev="cta_quote_service_install" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote">Pide presupuesto</a>
      </article>
      <article class="hub-card">
        <h3>Mantenimiento de climatizaci&oacute;n</h3>
        <p>Revisamos filtros, bater&iacute;as, desag&uuml;es y unidades exteriores para alargar la vida del equipo y reducir consumo.</p>
        <a class="btn btn-primary js-track" data-ev="cta_quote_service_maintenance" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote">Programar revisi&oacute;n</a>
      </article>
      <article class="hub-card">
        <h3>Reparaci&oacute;n de aire acondicionado</h3>
        <p>Atendemos equipos que no enfr&iacute;an, hacen ruido, pierden agua o muestran errores, con diagn&oacute;stico claro antes de reparar.</p>
        <a class="btn btn-primary js-track" data-ev="cta_quote_service_repair" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote">Consultar aver&iacute;a</a>
      </article>
      <article class="hub-card">
        <h3>Calefacci&oacute;n y bomba de calor</h3>
        <p>Soluciones eficientes para invierno y entretiempo, con equipos silenciosos y control por zonas cuando la vivienda lo necesita.</p>
        <a class="btn btn-primary js-track" data-ev="cta_quote_service_heatpump" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote">Valorar sistema</a>
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
      <div class="mini-map-card" aria-label="Esquema de cobertura en la Marina Baixa">
        <svg class="marina-map" viewBox="0 0 420 260" role="img" aria-labelledby="mini-map-title">
          <title id="mini-map-title">Cobertura principal en la Marina Baixa</title>
          <path class="sea" d="M255 0h165v260H220c34-43 43-80 34-120-8-36-2-82 1-140Z"></path>
          <path class="coast" d="M260 18c-32 36-24 69-12 102 13 36 6 75-28 116"></path>
          <path class="route" d="M78 172c45-32 83-47 136-56 40-7 70-23 98-55"></path>
          <g class="map-zone" tabindex="0">
            <circle class="zone-shape" cx="200" cy="150" r="32"></circle>
            <text class="map-label" x="200" y="154" text-anchor="middle">Benidorm</text>
          </g>
          <g class="map-zone" tabindex="0">
            <circle class="zone-shape" cx="245" cy="94" r="28"></circle>
            <text class="map-label" x="245" y="98" text-anchor="middle">Altea</text>
          </g>
          <g class="map-zone" tabindex="0">
            <circle class="zone-shape" cx="312" cy="58" r="28"></circle>
            <text class="map-label" x="312" y="62" text-anchor="middle">Calpe</text>
          </g>
          <g class="map-zone" tabindex="0">
            <circle class="zone-shape" cx="145" cy="128" r="28"></circle>
            <text class="map-label" x="145" y="132" text-anchor="middle">Finestrat</text>
          </g>
          <g class="map-zone" tabindex="0">
            <circle class="zone-shape" cx="178" cy="82" r="30"></circle>
            <text class="map-label" x="178" y="86" text-anchor="middle">La Nuc&iacute;a</text>
          </g>
        </svg>
      </div>
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
    <p class="hub-muted">Realizamos instalaciones, mantenimiento y reparaciones de climatizaci&oacute;n en estas localidades y alrededores.</p>
    <div class="zone-layout">
      <div class="zone-map-card">
        <svg class="marina-map" viewBox="0 0 520 340" role="img" aria-labelledby="zone-map-title zone-map-desc">
          <title id="zone-map-title">Mapa esquem&aacute;tico de servicio en Marina Baixa</title>
          <desc id="zone-map-desc">Las zonas principales se iluminan al pasar el cursor.</desc>
          <path class="sea" d="M350 0h170v340H312c48-63 59-112 47-164-12-49-13-104-9-176Z"></path>
          <path class="coast" d="M350 20c-38 45-35 82-18 130 17 51 5 103-38 166"></path>
          <path class="route" d="M88 222c62-44 114-66 184-78 48-8 92-33 130-78"></path>
          <path class="route" d="M126 104c44 24 93 35 151 34"></path>
          <g class="map-zone" tabindex="0">
            <path class="zone-shape" d="M237 178c31-7 58 12 60 43 1 31-24 54-55 49-27-5-47-28-43-55 3-20 17-32 38-37Z"></path>
            <text class="map-label" x="249" y="226" text-anchor="middle">Benidorm</text>
          </g>
          <g class="map-zone" tabindex="0">
            <path class="zone-shape" d="M296 105c31-12 61 4 67 35 5 31-17 57-49 56-28-1-51-21-52-48-1-20 12-35 34-43Z"></path>
            <text class="map-label" x="313" y="153" text-anchor="middle">Altea</text>
          </g>
          <g class="map-zone" tabindex="0">
            <path class="zone-shape" d="M382 50c26-11 54 3 61 30 7 28-12 53-41 54-26 1-49-17-51-41-2-19 10-34 31-43Z"></path>
            <text class="map-label" x="397" y="91" text-anchor="middle">Calpe</text>
          </g>
          <g class="map-zone" tabindex="0">
            <path class="zone-shape" d="M150 158c29-14 62-1 72 29 10 29-8 58-39 62-28 4-54-12-61-38-5-21 6-41 28-53Z"></path>
            <text class="map-label" x="172" y="207" text-anchor="middle">Finestrat</text>
          </g>
          <g class="map-zone" tabindex="0">
            <path class="zone-shape" d="M197 77c29-12 59 2 68 31 8 28-11 55-41 57-27 2-52-16-57-42-3-20 8-37 30-46Z"></path>
            <text class="map-label" x="218" y="119" text-anchor="middle">La Nuc&iacute;a</text>
          </g>
          <text class="map-label" x="440" y="292" text-anchor="middle">Costa Blanca</text>
        </svg>
      </div>
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

function hub_blog_body(): string {
  $intro = hub_intro(
    'Gu&iacute;as de climatizaci&oacute;n, aerotermia y aire acondicionado',
    'Consejos pr&aacute;cticos para elegir, mantener y aprovechar mejor tu sistema de climatizaci&oacute;n en la Costa Blanca.'
  );
  $styles = hub_styles();

  return <<<HTML
{$styles}
{$intro}
<section class="hub-section" aria-labelledby="blog-plan">
  <div class="container">
    <p class="hub-kicker">Gu&iacute;as</p>
    <h2 class="section-title" id="blog-plan">Biblioteca de gu&iacute;as &uacute;tiles</h2>
    <p class="hub-muted">Estamos preparando gu&iacute;as &uacute;tiles basadas en dudas reales de clientes: consumo, potencia necesaria, mantenimiento, bomba de calor, aerotermia, instalaci&oacute;n en viviendas, apartamentos tur&iacute;sticos y comunidades.</p>
    <div class="hub-grid guides-grid">
      <article class="hub-card">
        <h3>Aerotermia y bomba de calor: cu&aacute;ndo merece la pena</h3>
        <p>Una gu&iacute;a para entender cu&aacute;ndo una bomba de calor puede mejorar el confort y reducir el consumo.</p>
      </article>
      <article class="hub-card">
        <h3>Qu&eacute; potencia de aire acondicionado necesita una vivienda</h3>
        <p>Orientaci&oacute;n b&aacute;sica sobre metros cuadrados, aislamiento, orientaci&oacute;n y uso real.</p>
      </article>
      <article class="hub-card">
        <h3>Mantenimiento del aire acondicionado antes del verano</h3>
        <p>Revisiones, limpieza de filtros y se&ntilde;ales de aviso antes de la temporada de calor.</p>
      </article>
      <article class="hub-card">
        <h3>Aire acondicionado para apartamentos tur&iacute;sticos</h3>
        <p>Aspectos importantes para viviendas de alquiler vacacional: consumo, ruido, control y fiabilidad.</p>
      </article>
      <article class="hub-card">
        <h3>Split, multisplit o conductos: qu&eacute; sistema elegir</h3>
        <p>Diferencias principales entre sistemas y cu&aacute;ndo conviene cada soluci&oacute;n.</p>
      </article>
      <article class="hub-card">
        <h3>Climatizaci&oacute;n eficiente en la Costa Blanca</h3>
        <p>Consejos para viviendas en zonas de costa, humedad, calor prolongado y uso intensivo.</p>
      </article>
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
