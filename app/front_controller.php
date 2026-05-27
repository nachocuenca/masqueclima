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
