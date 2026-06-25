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

if (is_cookie_policy_path($path)) {
  // Redirect the legacy non-lang cookie policy path to the canonical ES version
  header('Location: /es/politica-de-cookies/', true, 301);
  exit;
}

$legalHtml = render_legal_page($path);
if ($legalHtml !== null) {
  $legalLang = detect_path_lang($path) ?? 'es';
  $GLOBALS['current_lang'] = $legalLang;
  $html = patch_snapshot_html($legalHtml, $path, $legalLang);
  header('Content-Type: text/html; charset=UTF-8');
  echo $html;
  exit;
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
  $html = patch_snapshot_google_reviews($html, $lang, $path);
  $html = patch_snapshot_quote_modal($html, $lang);
  $html = patch_snapshot_cookie_banner($html, $lang);
  $html = patch_snapshot_contact_anchor($html);
  $html = patch_snapshot_footer_guides_link($html, $lang);
  $html = patch_snapshot_home_context_links($html, $path, $lang);

  $html = patch_es_p1_location_page($html, $path, $lang);
  $html = patch_nonES_locality_seo($html, $path, $lang);
  $html = patch_locality_hero_image($html, $path, $lang);
  $html = patch_snapshot_og_image($html, $path, $lang);

  $html = patch_snapshot_home_hero($html, $path, $lang);
  $html = patch_snapshot_demand_notice($html, $path, $lang);

  $statusScript = snapshot_feedback_script();
  if ($statusScript !== '') {
    $html = str_replace('</body>', $statusScript . "\n</body>", $html);
  }

  // Inject Cloudflare Turnstile script if enabled
  $turnstileEnabled = (bool) config('turnstile.enabled', false);
  $turnstileSiteKey = (string) (config('turnstile.site_key') ?? '');
  if ($turnstileEnabled && $turnstileSiteKey !== '') {
    $tsScript = '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
    if (!str_contains($html, 'challenges.cloudflare.com/turnstile')) {
      $html = str_replace('</body>', $tsScript . "\n</body>", $html);
    }
  }

  $parts = explode('</head>', $html, 2);
  if (count($parts) === 2) {
    $parts[1] = patch_snapshot_body_links($parts[1], $lang, $path);
    $html = $parts[0] . '</head>' . $parts[1];
  }

  $html = patch_snapshot_asset_versions($html);

  return $html;
}

function patch_snapshot_asset_versions(string $html): string {
  $assets = [
    'href' => '/assets/css/styles.css',
    'src' => '/assets/js/main.js',
  ];

  foreach ($assets as $attr => $assetPath) {
    $versionedUrl = versioned_asset($assetPath);
    $pattern = '/\b' . preg_quote($attr, '/') . '\s*=\s*(["\'])' . preg_quote($assetPath, '/') . '(?:\?[^"\']*)?\1/i';
    $html = preg_replace_callback(
      $pattern,
      static function (array $matches) use ($attr, $versionedUrl): string {
        $quote = $matches[1];
        return $attr . '=' . $quote . e($versionedUrl) . $quote;
      },
      $html
    ) ?? $html;
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

function demand_notice_styles(): string {
  return 'padding:.75rem .9rem;border:1px solid #f3d18c;border-left:4px solid #d88916;border-radius:8px;background:#fff8e8;color:#4d3413;font-size:.92rem;line-height:1.45;';
}

function demand_notice_html(string $variant = 'home'): string {
  $lang = (string) ($GLOBALS['current_lang'] ?? 'es');
  $labels = heatwave_notice_labels($lang);

  return '<div class="heatwave-form-notice" role="note" aria-label="' . e($labels['aria']) . '" data-demand-notice="1" style="margin:0 0 1rem;' . demand_notice_styles() . '">' .
    '<span aria-hidden="true" style="display:inline-block;margin-right:.35rem;">&#9728;</span>' .
    '<span>' . $labels['form'] . '</span>' .
    '</div>';
}

function heatwave_notice_labels(string $lang): array {
  $labels = [
    'es' => [
      'aria' => 'Aviso por alta demanda',
      'banner' => 'Alta demanda por ola de calor: estamos recibiendo muchas solicitudes y podemos tardar m&aacute;s de lo habitual en responder. Las nuevas citas pueden tener una espera aproximada de un mes. Damos prioridad a incidencias urgentes de clientes actuales.',
      'banner_mobile' => 'Ola de calor: alta demanda y respuesta m&aacute;s lenta. Nuevas citas: espera aprox. de un mes. Urgencias de clientes actuales, prioridad.',
      'form' => 'Alta demanda por ola de calor: las nuevas citas pueden tener una espera aproximada de un mes. Priorizamos incidencias urgentes de clientes actuales.',
    ],
    'en' => [
      'aria' => 'High demand notice',
      'banner' => 'High demand due to the heatwave: we are receiving many requests and may take longer than usual to respond. New appointments may have an estimated wait of around one month. We prioritise urgent issues for existing customers.',
      'banner_mobile' => 'Heatwave: high demand and slower replies. New appointments: about a one-month wait. Urgent issues for existing customers take priority.',
      'form' => 'High demand due to the heatwave: new appointments may have an estimated wait of around one month. We prioritise urgent issues for existing customers.',
    ],
    'de' => [
      'aria' => 'Hinweis zu hoher Nachfrage',
      'banner' => 'Hohe Nachfrage wegen der Hitzewelle: Wir erhalten viele Anfragen und die Antwort kann l&auml;nger als gewohnt dauern. Neue Termine k&ouml;nnen derzeit etwa einen Monat Wartezeit haben. Dringende Anliegen bestehender Kunden haben Vorrang.',
      'banner_mobile' => 'Hitzewelle: hohe Nachfrage, Antworten dauern l&auml;nger. Neue Termine: ca. ein Monat Wartezeit. Notf&auml;lle bestehender Kunden haben Vorrang.',
      'form' => 'Hohe Nachfrage wegen der Hitzewelle: Neue Termine k&ouml;nnen derzeit etwa einen Monat Wartezeit haben. Dringende Anliegen bestehender Kunden haben Vorrang.',
    ],
    'nl' => [
      'aria' => 'Melding hoge vraag',
      'banner' => 'Hoge vraag door de hittegolf: we ontvangen veel aanvragen en antwoorden mogelijk later dan normaal. Voor nieuwe afspraken kan de wachttijd ongeveer een maand zijn. Spoedgevallen van bestaande klanten krijgen prioriteit.',
      'banner_mobile' => 'Hittegolf: hoge vraag en tragere reacties. Nieuwe afspraken: ongeveer een maand wachttijd. Spoed voor bestaande klanten krijgt prioriteit.',
      'form' => 'Hoge vraag door de hittegolf: voor nieuwe afspraken kan de wachttijd ongeveer een maand zijn. Spoedgevallen van bestaande klanten krijgen prioriteit.',
    ],
    'ru' => [
      'aria' => '&#1059;&#1074;&#1077;&#1076;&#1086;&#1084;&#1083;&#1077;&#1085;&#1080;&#1077; &#1086; &#1074;&#1099;&#1089;&#1086;&#1082;&#1086;&#1084; &#1089;&#1087;&#1088;&#1086;&#1089;&#1077;',
      'banner' => '&#1042;&#1099;&#1089;&#1086;&#1082;&#1080;&#1081; &#1089;&#1087;&#1088;&#1086;&#1089; &#1080;&#1079;-&#1079;&#1072; &#1078;&#1072;&#1088;&#1099;: &#1084;&#1099; &#1087;&#1086;&#1083;&#1091;&#1095;&#1072;&#1077;&#1084; &#1084;&#1085;&#1086;&#1075;&#1086; &#1079;&#1072;&#1087;&#1088;&#1086;&#1089;&#1086;&#1074; &#1080; &#1084;&#1086;&#1078;&#1077;&#1084; &#1086;&#1090;&#1074;&#1077;&#1095;&#1072;&#1090;&#1100; &#1076;&#1086;&#1083;&#1100;&#1096;&#1077; &#1086;&#1073;&#1099;&#1095;&#1085;&#1086;&#1075;&#1086;. &#1053;&#1086;&#1074;&#1099;&#1077; &#1074;&#1080;&#1079;&#1080;&#1090;&#1099; &#1084;&#1086;&#1075;&#1091;&#1090; &#1080;&#1084;&#1077;&#1090;&#1100; &#1086;&#1078;&#1080;&#1076;&#1072;&#1085;&#1080;&#1077; &#1086;&#1082;&#1086;&#1083;&#1086; &#1086;&#1076;&#1085;&#1086;&#1075;&#1086; &#1084;&#1077;&#1089;&#1103;&#1094;&#1072;. &#1057;&#1088;&#1086;&#1095;&#1085;&#1099;&#1077; &#1089;&#1083;&#1091;&#1095;&#1072;&#1080; &#1090;&#1077;&#1082;&#1091;&#1097;&#1080;&#1093; &#1082;&#1083;&#1080;&#1077;&#1085;&#1090;&#1086;&#1074; &#1074; &#1087;&#1088;&#1080;&#1086;&#1088;&#1080;&#1090;&#1077;.',
      'banner_mobile' => '&#1046;&#1072;&#1088;&#1072;: &#1074;&#1099;&#1089;&#1086;&#1082;&#1080;&#1081; &#1089;&#1087;&#1088;&#1086;&#1089;, &#1086;&#1090;&#1074;&#1077;&#1095;&#1072;&#1077;&#1084; &#1076;&#1086;&#1083;&#1100;&#1096;&#1077;. &#1053;&#1086;&#1074;&#1099;&#1077; &#1074;&#1080;&#1079;&#1080;&#1090;&#1099;: &#1086;&#1078;&#1080;&#1076;&#1072;&#1085;&#1080;&#1077; &#1086;&#1082;&#1086;&#1083;&#1086; &#1084;&#1077;&#1089;&#1103;&#1094;&#1072;. &#1057;&#1088;&#1086;&#1095;&#1085;&#1099;&#1077; &#1089;&#1083;&#1091;&#1095;&#1072;&#1080; &#1090;&#1077;&#1082;&#1091;&#1097;&#1080;&#1093; &#1082;&#1083;&#1080;&#1077;&#1085;&#1090;&#1086;&#1074; &#1074; &#1087;&#1088;&#1080;&#1086;&#1088;&#1080;&#1090;&#1077;.',
      'form' => '&#1042;&#1099;&#1089;&#1086;&#1082;&#1080;&#1081; &#1089;&#1087;&#1088;&#1086;&#1089; &#1080;&#1079;-&#1079;&#1072; &#1078;&#1072;&#1088;&#1099;: &#1085;&#1086;&#1074;&#1099;&#1077; &#1074;&#1080;&#1079;&#1080;&#1090;&#1099; &#1084;&#1086;&#1075;&#1091;&#1090; &#1080;&#1084;&#1077;&#1090;&#1100; &#1086;&#1078;&#1080;&#1076;&#1072;&#1085;&#1080;&#1077; &#1086;&#1082;&#1086;&#1083;&#1086; &#1086;&#1076;&#1085;&#1086;&#1075;&#1086; &#1084;&#1077;&#1089;&#1103;&#1094;&#1072;. &#1057;&#1088;&#1086;&#1095;&#1085;&#1099;&#1077; &#1089;&#1083;&#1091;&#1095;&#1072;&#1080; &#1090;&#1077;&#1082;&#1091;&#1097;&#1080;&#1093; &#1082;&#1083;&#1080;&#1077;&#1085;&#1090;&#1086;&#1074; &#1074; &#1087;&#1088;&#1080;&#1086;&#1088;&#1080;&#1090;&#1077;.',
    ],
    'no' => [
      'aria' => 'Varsel om stor p&aring;gang',
      'banner' => 'Stor p&aring;gang p&aring; grunn av heteb&oslash;lgen: vi mottar mange henvendelser og kan bruke lenger tid enn vanlig p&aring; &aring; svare. Nye avtaler kan ha omtrent en m&aring;neds ventetid. Vi prioriterer akutte saker for eksisterende kunder.',
      'banner_mobile' => 'Heteb&oslash;lge: stor p&aring;gang og tregere svar. Nye avtaler: ca. &eacute;n m&aring;neds ventetid. Akutte saker for eksisterende kunder prioriteres.',
      'form' => 'Stor p&aring;gang p&aring; grunn av heteb&oslash;lgen: nye avtaler kan ha omtrent en m&aring;neds ventetid. Vi prioriterer akutte saker for eksisterende kunder.',
    ],
  ];

  return $labels[$lang] ?? $labels['es'];
}

function heatwave_top_banner_html(string $lang): string {
  $labels = heatwave_notice_labels($lang);
  return '<div class="heatwave-top-banner" role="note" aria-label="' . e($labels['aria']) . '" data-heatwave-banner="1" style="box-sizing:border-box;width:100%;background:#fff3d6;border-bottom:1px solid #efc56f;color:#4b3412;font-size:.9rem;line-height:1.28;">' .
    '<div style="box-sizing:border-box;width:100%;max-width:1180px;margin:0 auto;padding:.42rem .95rem;display:flex;align-items:center;gap:.65rem;">' .
    '<span class="heatwave-banner-icon" aria-hidden="true" style="flex:0 0 auto;display:inline-flex;align-items:center;justify-content:center;width:1.55rem;height:1.55rem;font-size:1.35rem;line-height:1;">&#9728;</span>' .
    '<span class="heatwave-text-desktop" style="min-width:0;flex:1 1 auto;overflow-wrap:anywhere;">' . $labels['banner'] . '</span>' .
    '<span class="heatwave-text-mobile" style="min-width:0;flex:1 1 auto;overflow-wrap:anywhere;">' . ($labels['banner_mobile'] ?? $labels['banner']) . '</span>' .
    '</div>' .
    '</div>';
}

function patch_snapshot_demand_notice(string $html, string $path, string $lang): string {
  if (!str_contains($html, 'data-heatwave-banner="1"')) {
    $banner = heatwave_top_banner_html($lang);
    $html = preg_replace('/<header\b([^>]*)>/i', '<header$1 style="position:sticky;top:0;z-index:1040;width:100%;">' . "\n" . $banner, $html, 1) ?? $html;
    $html = str_replace('</head>', heatwave_layout_overrides() . "\n</head>", $html);
  }

  if (str_contains($html, 'id="presupuesto"') && !str_contains($html, 'data-demand-contact-notice="1"')) {
    $contactNotice = '<div data-demand-contact-notice="1">' . demand_notice_html('contact') . '</div>';
    $html = preg_replace('/(<section\b[^>]*\bid="presupuesto"[^>]*>\s*<div class="container">)/i', '$1' . "\n    " . $contactNotice, $html, 1) ?? $html;
  }

  return $html;
}

function heatwave_layout_overrides(): string {
  return '<style>html{scroll-padding-top:140px}body .hero{margin-top:0!important}header{position:sticky!important;top:0!important;z-index:1040!important;width:100%!important}.navbar.fixed-top{position:static!important;top:auto!important}.heatwave-top-banner{position:relative!important}.heatwave-text-mobile{display:none}.heatwave-form-notice{box-shadow:none!important}[id]{scroll-margin-top:140px}@media(max-width:767.98px){html{scroll-padding-top:150px}[id]{scroll-margin-top:150px}.heatwave-top-banner{font-size:.79rem!important;line-height:1.22!important}.heatwave-top-banner>div{padding:.32rem .72rem!important;gap:.5rem!important;align-items:center!important}.heatwave-banner-icon{width:1.65rem!important;height:1.65rem!important;font-size:1.45rem!important}.heatwave-text-desktop{display:none!important}.heatwave-text-mobile{display:inline!important}}</style>';
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

function quote_modal_labels(string $lang): array {
  $labels = [
    'es' => [
      'close' => 'Cerrar',
      'title' => 'Solicita tu presupuesto',
      'intro' => 'Deja tus datos y te responderemos r&aacute;pido.',
      'name' => 'Nombre *',
      'phone' => 'Tel&eacute;fono *',
      'email' => 'Email',
      'service' => 'Tipo de servicio',
      'select' => 'Selecciona un servicio',
      'install' => 'Instalaci&oacute;n de aire acondicionado',
      'maintenance' => 'Mantenimiento de climatizaci&oacute;n',
      'heating' => 'Instalaci&oacute;n de calefacci&oacute;n',
      'electrical' => 'Instalaciones el&eacute;ctricas',
      'plumbing' => 'Servicios de fontaner&iacute;a',
      'urgent' => 'Servicio urgente 24/7',
      'message' => 'Descripci&oacute;n del trabajo',
      'submit' => 'Enviar solicitud',
      'cancel' => 'Cancelar',
    ],
    'en' => [
      'close' => 'Close',
      'title' => 'Get a free quote',
      'intro' => 'Leave your details and we will get back to you quickly.',
      'name' => 'Name *',
      'phone' => 'Phone *',
      'email' => 'Email',
      'service' => 'Type of service',
      'select' => 'Select a service',
      'install' => 'Air conditioning installation',
      'maintenance' => 'HVAC maintenance',
      'heating' => 'Heating installation',
      'electrical' => 'Electrical installations',
      'plumbing' => 'Plumbing services',
      'urgent' => 'Urgent service 24/7',
      'message' => 'Job description',
      'submit' => 'Send request',
      'cancel' => 'Cancel',
    ],
    'de' => [
      'close' => 'Schlie&szlig;en',
      'title' => 'Unverbindliches Angebot anfordern',
      'intro' => 'Hinterlassen Sie Ihre Daten, wir melden uns schnell.',
      'name' => 'Name *',
      'phone' => 'Telefon *',
      'email' => 'E-Mail',
      'service' => 'Leistungsart',
      'select' => 'Dienst ausw&auml;hlen',
      'install' => 'Klimaanlagen-Installation',
      'maintenance' => 'Wartung von Klimasystemen',
      'heating' => 'Heizungsinstallation',
      'electrical' => 'Elektroinstallationen',
      'plumbing' => 'Sanit&auml;rleistungen',
      'urgent' => '24/7-Notdienst',
      'message' => 'Auftragsbeschreibung',
      'submit' => 'Anfrage senden',
      'cancel' => 'Abbrechen',
    ],
    'nl' => [
      'close' => 'Sluiten',
      'title' => 'Vraag een vrijblijvende offerte aan',
      'intro' => 'Laat je gegevens achter, we reageren snel.',
      'name' => 'Naam *',
      'phone' => 'Telefoon *',
      'email' => 'E-mail',
      'service' => 'Type dienst',
      'select' => 'Kies een dienst',
      'install' => 'Airco-installatie',
      'maintenance' => 'Onderhoud HVAC',
      'heating' => 'Verwarmingsinstallatie',
      'electrical' => 'Elektrische installaties',
      'plumbing' => 'Loodgieterswerk',
      'urgent' => '24/7 spoedservice',
      'message' => 'Werkbeschrijving',
      'submit' => 'Versturen',
      'cancel' => 'Annuleren',
    ],
    'ru' => [
      'close' => '&#1047;&#1072;&#1082;&#1088;&#1099;&#1090;&#1100;',
      'title' => '&#1047;&#1072;&#1087;&#1088;&#1086;&#1089;&#1080;&#1090;&#1100; &#1087;&#1088;&#1077;&#1076;&#1083;&#1086;&#1078;&#1077;&#1085;&#1080;&#1077;',
      'intro' => '&#1054;&#1089;&#1090;&#1072;&#1074;&#1100;&#1090;&#1077; &#1082;&#1086;&#1085;&#1090;&#1072;&#1082;&#1090;&#1099;, &#1084;&#1099; &#1073;&#1099;&#1089;&#1090;&#1088;&#1086; &#1089;&#1074;&#1103;&#1078;&#1077;&#1084;&#1089;&#1103;.',
      'name' => '&#1048;&#1084;&#1103; *',
      'phone' => '&#1058;&#1077;&#1083;&#1077;&#1092;&#1086;&#1085; *',
      'email' => 'Email',
      'service' => '&#1058;&#1080;&#1087; &#1091;&#1089;&#1083;&#1091;&#1075;&#1080;',
      'select' => '&#1042;&#1099;&#1073;&#1077;&#1088;&#1080;&#1090;&#1077; &#1091;&#1089;&#1083;&#1091;&#1075;&#1091;',
      'install' => '&#1052;&#1086;&#1085;&#1090;&#1072;&#1078; &#1082;&#1086;&#1085;&#1076;&#1080;&#1094;&#1080;&#1086;&#1085;&#1077;&#1088;&#1086;&#1074;',
      'maintenance' => '&#1054;&#1073;&#1089;&#1083;&#1091;&#1078;&#1080;&#1074;&#1072;&#1085;&#1080;&#1077; &#1082;&#1083;&#1080;&#1084;&#1072;&#1090;&#1080;&#1095;&#1077;&#1089;&#1082;&#1080;&#1093; &#1089;&#1080;&#1089;&#1090;&#1077;&#1084;',
      'heating' => '&#1052;&#1086;&#1085;&#1090;&#1072;&#1078; &#1086;&#1090;&#1086;&#1087;&#1083;&#1077;&#1085;&#1080;&#1103;',
      'electrical' => '&#1069;&#1083;&#1077;&#1082;&#1090;&#1088;&#1086;&#1084;&#1086;&#1085;&#1090;&#1072;&#1078;&#1085;&#1099;&#1077; &#1088;&#1072;&#1073;&#1086;&#1090;&#1099;',
      'plumbing' => '&#1057;&#1072;&#1085;&#1090;&#1077;&#1093;&#1085;&#1080;&#1095;&#1077;&#1089;&#1082;&#1080;&#1077; &#1091;&#1089;&#1083;&#1091;&#1075;&#1080;',
      'urgent' => '&#1057;&#1088;&#1086;&#1095;&#1085;&#1099;&#1081; &#1074;&#1099;&#1077;&#1079;&#1076; 24/7',
      'message' => '&#1054;&#1087;&#1080;&#1089;&#1072;&#1085;&#1080;&#1077; &#1088;&#1072;&#1073;&#1086;&#1090;',
      'submit' => '&#1054;&#1090;&#1087;&#1088;&#1072;&#1074;&#1080;&#1090;&#1100; &#1079;&#1072;&#1103;&#1074;&#1082;&#1091;',
      'cancel' => '&#1054;&#1090;&#1084;&#1077;&#1085;&#1072;',
    ],
    'no' => [
      'close' => 'Lukk',
      'title' => 'Be om tilbud',
      'intro' => 'Legg igjen kontaktinfo, s&aring; svarer vi raskt.',
      'name' => 'Navn *',
      'phone' => 'Telefon *',
      'email' => 'E-post',
      'service' => 'Tjenestetype',
      'select' => 'Velg en tjeneste',
      'install' => 'Installasjon av aircondition',
      'maintenance' => 'Vedlikehold av klimasystemer',
      'heating' => 'Installasjon av oppvarming',
      'electrical' => 'Elektriske installasjoner',
      'plumbing' => 'R&oslash;rleggertjenester',
      'urgent' => 'Utrykning 24/7',
      'message' => 'Beskrivelse av jobben',
      'submit' => 'Send foresp&oslash;rsel',
      'cancel' => 'Avbryt',
    ],
  ];

  return $labels[$lang] ?? $labels['es'];
}

function patch_snapshot_quote_modal(string $html, string $lang): string {
  $start = strpos($html, '<div class="modal fade quote-modal" id="quoteModal"');
  if ($start === false) {
    return $html;
  }

  $end = strpos($html, '<!-- =======================', $start + 1);
  if ($end === false) {
    return $html;
  }

  $labels = quote_modal_labels($lang);
  $modal = substr($html, $start, $end - $start);
  if (!str_contains($modal, 'for="q-name"')) {
    return $html;
  }

  $modal = preg_replace('/(<button type="button" class="btn-close[^"]*"[^>]*aria-label=")[^"]*(")/i', '$1' . e(html_entity_decode($labels['close'], ENT_QUOTES | ENT_HTML5, 'UTF-8')) . '$2', $modal, 1) ?? $modal;
  $modal = preg_replace('/(<h3 class="mb-2 fw-bold">)[\s\S]*?(<\/h3>)/', '$1' . $labels['title'] . '$2', $modal, 1) ?? $modal;
  $modal = preg_replace('/(<p class="text-muted mb-4">)[\s\S]*?(<\/p>)/', '$1' . $labels['intro'] . '$2', $modal, 1) ?? $modal;
  if (!str_contains($modal, 'data-demand-modal-notice="1"')) {
    $modal = preg_replace(
      '/(<form method="post" action="\/contact-submit\.php")/i',
      '<div data-demand-modal-notice="1">' . demand_notice_html('contact') . '</div>' . "\n\n        " . '$1',
      $modal,
      1
    ) ?? $modal;
  }

  foreach ([
    'q-name' => 'name',
    'q-phone' => 'phone',
    'q-email' => 'email',
    'q-service' => 'service',
    'q-msg' => 'message',
  ] as $for => $key) {
    $modal = preg_replace('/(<label for="' . preg_quote($for, '/') . '" class="form-label">)[\s\S]*?(<\/label>)/', '$1' . $labels[$key] . '$2', $modal, 1) ?? $modal;
  }

  foreach ([
    '' => 'select',
    'climatizacion' => 'install',
    'mantenimiento' => 'maintenance',
    'calefaccion' => 'heating',
    'electricidad' => 'electrical',
    'fontaneria' => 'plumbing',
    'urgente' => 'urgent',
  ] as $value => $key) {
    $modal = preg_replace('/(<option value="' . preg_quote($value, '/') . '">)[\s\S]*?(<\/option>)/', '$1' . $labels[$key] . '$2', $modal, 1) ?? $modal;
  }

  $modal = preg_replace('/(<button type="submit" class="btn btn-primary btn-lg px-4">)[\s\S]*?(<\/button>)/', '$1' . "\n              " . $labels['submit'] . '            $2', $modal, 1) ?? $modal;
  $modal = preg_replace('/(<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">)[\s\S]*?(<\/button>)/', '$1' . "\n              " . $labels['cancel'] . '            $2', $modal, 1) ?? $modal;

  // Inject Turnstile widget before submit button if enabled and not already present
  $turnstileEnabled  = (bool) config('turnstile.enabled', false);
  $turnstileSiteKey  = (string) (config('turnstile.site_key') ?? '');
  if ($turnstileEnabled && $turnstileSiteKey !== '' && !str_contains($modal, 'cf-turnstile')) {
    $tsWidget = "\n            <div class=\"cf-turnstile mb-3\" data-sitekey=\"" . e($turnstileSiteKey) . "\" data-theme=\"light\" data-language=\"" . e($lang) . "\"></div>";
    $modal = preg_replace(
      '/(<button type="submit" class="btn btn-primary btn-lg px-4">)/',
      $tsWidget . "\n            $1",
      $modal,
      1
    ) ?? $modal;
  }

  return substr($html, 0, $start) . $modal . substr($html, $end);
}

function cookie_banner_labels(string $lang): array {
  $labels = [
    'es' => [
      'message' => 'Usamos cookies para anal&iacute;tica (GA4) y contenidos de terceros (Maps, Elfsight).',
      'more' => 'M&aacute;s info',
      'reject' => 'Rechazar',
      'analytics' => 'Solo anal&iacute;ticas',
      'accept' => 'Aceptar',
    ],
    'en' => [
      'message' => 'We use cookies for analytics (GA4) and third-party content (Maps, Elfsight).',
      'more' => 'More info',
      'reject' => 'Reject',
      'analytics' => 'Analytics only',
      'accept' => 'Accept',
    ],
    'de' => [
      'message' => 'Wir nutzen Cookies f&uuml;r Analyse (GA4) und Inhalte Dritter (Maps, Elfsight).',
      'more' => 'Mehr Infos',
      'reject' => 'Ablehnen',
      'analytics' => 'Nur Analyse',
      'accept' => 'Akzeptieren',
    ],
    'nl' => [
      'message' => 'We gebruiken cookies voor analytics (GA4) en content van derden (Maps, Elfsight).',
      'more' => 'Meer info',
      'reject' => 'Weigeren',
      'analytics' => 'Alleen analytics',
      'accept' => 'Accepteren',
    ],
    'ru' => [
      'message' => '&#1052;&#1099; &#1080;&#1089;&#1087;&#1086;&#1083;&#1100;&#1079;&#1091;&#1077;&#1084; cookies &#1076;&#1083;&#1103; &#1072;&#1085;&#1072;&#1083;&#1080;&#1090;&#1080;&#1082;&#1080; (GA4) &#1080; &#1082;&#1086;&#1085;&#1090;&#1077;&#1085;&#1090;&#1072; &#1090;&#1088;&#1077;&#1090;&#1100;&#1080;&#1093; &#1089;&#1090;&#1086;&#1088;&#1086;&#1085; (Maps, Elfsight).',
      'more' => '&#1055;&#1086;&#1076;&#1088;&#1086;&#1073;&#1085;&#1077;&#1077;',
      'reject' => '&#1054;&#1090;&#1082;&#1083;&#1086;&#1085;&#1080;&#1090;&#1100;',
      'analytics' => '&#1058;&#1086;&#1083;&#1100;&#1082;&#1086; &#1072;&#1085;&#1072;&#1083;&#1080;&#1090;&#1080;&#1082;&#1072;',
      'accept' => '&#1055;&#1088;&#1080;&#1085;&#1103;&#1090;&#1100;',
    ],
    'no' => [
      'message' => 'Vi bruker informasjonskapsler for analyse (GA4) og tredjepartsinnhold (Maps, Elfsight).',
      'more' => 'Mer info',
      'reject' => 'Avvis',
      'analytics' => 'Bare analyse',
      'accept' => 'Godta',
    ],
  ];

  return $labels[$lang] ?? $labels['es'];
}

function patch_snapshot_cookie_banner(string $html, string $lang): string {
  if (!str_contains($html, 'id="cookie-banner"')) {
    return $html;
  }

  $labels = cookie_banner_labels($lang);
  $cookieUrl = e(legal_cookie_policy_url($lang));

  return preg_replace(
    '/(<div id="cookie-banner" class="cookie-banner" hidden>\s*<div class="cookie-box">\s*<p>)[\s\S]*?(<a href="[^"]*" target="_blank" rel="nofollow">)[\s\S]*?(<\/a>\s*<\/p>\s*<div class="cookie-actions">\s*<button id="cb-reject"[^>]*>)[\s\S]*?(<\/button>\s*<button id="cb-analytics"[^>]*>)[\s\S]*?(<\/button>\s*<button id="cb-accept"[^>]*>)[\s\S]*?(<\/button>)/',
    '$1' . "\n      " . $labels['message'] . "\n      " . '<a href="' . $cookieUrl . '" target="_blank" rel="nofollow">' . $labels['more'] . '$3' . $labels['reject'] . '$4' . $labels['analytics'] . '$5' . $labels['accept'] . '$6',
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
    '<span id="contacto" aria-hidden="true" style="display:block;height:0;scroll-margin-top:170px;"></span>' . "\n" . '$1',
    $html,
    1
  ) ?? $html;
}

function patch_snapshot_google_reviews(string $html, string $lang, string $path): string {
  $path = normalize_snapshot_path($path);
  if (is_legal_page_path($path) || str_contains($html, 'class="google-reviews-section"') || !str_contains($html, 'id="presupuesto"')) {
    return $html;
  }

  $reviewsHtml = google_reviews_html($lang);
  if (trim($reviewsHtml) === '') {
    return $html;
  }

  return preg_replace(
    '/(<section\b[^>]*\bid="presupuesto"[^>]*>)/i',
    $reviewsHtml . "\n" . '$1',
    $html,
    1
  ) ?? $html;
}

function is_legal_page_path(string $path): bool {
  $normalized = normalize_snapshot_path($path);
  foreach (legal_hreflang_map() as $group) {
    if (!is_array($group)) {
      continue;
    }
    foreach ($group as $candidate) {
      if (is_string($candidate) && normalize_snapshot_path($candidate) === $normalized) {
        return true;
      }
    }
  }
  return false;
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
  $servicesUrl = localized_hub_url($lang, 'services');
  $zonesUrl    = localized_hub_url($lang, 'zones');
  $guidesUrl   = localized_hub_url($lang, 'guides');
  $labels = [
    'services' => t('nav.services', $lang === 'es' ? 'Servicios' : 'Services'),
    'zones'    => t('nav.zones', $lang === 'es' ? 'Zonas' : 'Areas'),
    'guides'   => t('nav.guides', $lang === 'es' ? 'Gu&iacute;as' : 'Guides'),
  ];
  $items = [];

  if ($servicesUrl !== null) {
    $items[] = '<a class="text-white" href="' . e($servicesUrl) . '">' . e(html_entity_decode((string) $labels['services'], ENT_QUOTES | ENT_HTML5, 'UTF-8')) . '</a>';
  }
  if ($zonesUrl !== null) {
    $items[] = '<a class="text-white" href="' . e($zonesUrl) . '">' . e(html_entity_decode((string) $labels['zones'], ENT_QUOTES | ENT_HTML5, 'UTF-8')) . '</a>';
  }
  if ($guidesUrl !== null) {
    $items[] = '<a class="text-white" href="' . e($guidesUrl) . '">' . e(html_entity_decode((string) $labels['guides'], ENT_QUOTES | ENT_HTML5, 'UTF-8')) . '</a>';
  }

  $navRow = '<p class="mb-1 footer-main-links">' . implode(' | ', $items) . '</p>';

  // Legal links row
  $legalLinks = footer_legal_links_html($lang);

  return $navRow . "\n    " . $legalLinks;
}

function footer_legal_links_html(string $lang): string {
  $hreflangMap = legal_hreflang_map();
  $cookiesUrl  = $hreflangMap['cookies'][$lang]  ?? $hreflangMap['cookies']['es'];
  $privacyUrl  = $hreflangMap['privacy'][$lang]  ?? $hreflangMap['privacy']['es'];
  $legalUrl    = $hreflangMap['legal'][$lang]     ?? $hreflangMap['legal']['es'];

  $labels = [
    'es' => ['legal' => 'Aviso legal',           'privacy' => 'Privacidad',    'cookies' => 'Cookies'],
    'en' => ['legal' => 'Legal notice',           'privacy' => 'Privacy',       'cookies' => 'Cookies'],
    'de' => ['legal' => 'Impressum',              'privacy' => 'Datenschutz',   'cookies' => 'Cookies'],
    'nl' => ['legal' => 'Juridische mededeling',  'privacy' => 'Privacy',       'cookies' => 'Cookies'],
    'ru' => ['legal' => 'Правовое уведомление',   'privacy' => 'Конфиденциальность', 'cookies' => 'Cookies'],
    'no' => ['legal' => 'Juridisk varsel',         'privacy' => 'Personvern',    'cookies' => 'Cookies'],
  ];
  $l = $labels[$lang] ?? $labels['es'];

  return '<p class="mb-0 footer-legal-links" style="font-size:.8rem;opacity:.7;">'
    . '<a class="text-white" href="' . e($legalUrl) . '">' . e($l['legal']) . '</a>'
    . ' &middot; '
    . '<a class="text-white" href="' . e($privacyUrl) . '">' . e($l['privacy']) . '</a>'
    . ' &middot; '
    . '<a class="text-white" href="' . e($cookiesUrl) . '">' . e($l['cookies']) . '</a>'
    . '</p>';
}

function patch_snapshot_home_context_links(string $html, string $path, string $lang): string {
  if (!snapshot_is_home_path($path, $lang) || str_contains($html, 'home-context-links')) {
    return $html;
  }

  $labels = [
    'es' => ['services' => 'Ver servicios de climatizaci&oacute;n', 'zones' => 'Ver zonas de servicio', 'guides' => 'Ver gu&iacute;as de climatizaci&oacute;n'],
    'en' => ['services' => 'Services', 'zones' => 'Areas', 'guides' => 'Guides'],
    'de' => ['services' => 'Dienstleistungen', 'zones' => 'Gebiete', 'guides' => 'Ratgeber'],
    'nl' => ['services' => 'Diensten', 'zones' => 'Gebieden', 'guides' => 'Gidsen'],
    'ru' => ['services' => '&#1059;&#1089;&#1083;&#1091;&#1075;&#1080;', 'zones' => '&#1056;&#1072;&#1081;&#1086;&#1085;&#1099;', 'guides' => '&#1043;&#1080;&#1076;&#1099;'],
    'no' => ['services' => 'Tjenester', 'zones' => 'Omr&aring;der', 'guides' => 'Guider'],
  ];
  $copy = $labels[$lang] ?? $labels['es'];
  $servicesUrl = localized_hub_url($lang, 'services') ?? lang_url($lang);
  $zonesUrl = localized_hub_url($lang, 'zones') ?? lang_url($lang);
  $guidesUrl = localized_hub_url($lang, 'guides') ?? lang_url($lang);

  $servicesLink = '    <p class="mt-4 mb-0 text-center home-context-links"><a class="btn btn-outline-primary" href="' . e($servicesUrl) . '">' . $copy['services'] . '</a></p>' . "\n";
  $html = preg_replace_callback(
    '/(<section class="services-plain" id="metodo">[\s\S]*?)(\s*<\/div>\s*<\/section>)/',
    static function (array $matches) use ($servicesLink): string {
      return rtrim($matches[1]) . "\n" . $servicesLink . $matches[2];
    },
    $html,
    1
  ) ?? $html;

  $zonesLink = '    <p class="mt-3 mb-4 text-center home-context-links"><a class="btn btn-outline-primary" href="' . e($zonesUrl) . '">' . $copy['zones'] . '</a></p>' . "\n";
  $zonesNeedle = '    <div class="zona-mapa">';
  if (str_contains($html, $zonesNeedle)) {
    $html = str_replace($zonesNeedle, $zonesLink . $zonesNeedle, $html);
  }

  $guidesLink = '    <p class="text-center mb-4 home-context-links"><a class="btn btn-outline-primary" href="' . e($guidesUrl) . '">' . $copy['guides'] . '</a></p>' . "\n";
  $faqNeedle = '    <div class="accordion" id="faqAccordion">';
  if (str_contains($html, $faqNeedle)) {
    $html = str_replace($faqNeedle, $guidesLink . $faqNeedle, $html);
  }

  return $html;
}

function patch_snapshot_body_links(string $body, string $lang, string $path): string {
  return preg_replace_callback(
    '/\bhref=(["\'])(.*?)\1/i',
    static function (array $m) use ($lang, $path): string {
      return 'href=' . $m[1] . rewrite_visible_href($m[2], $lang, $path) . $m[1];
    },
    $body
  ) ?? $body;
}

function rewrite_visible_href(string $href, string $lang, string $currentPath): string {
  $langs = config('brand.langs', ['es']);
  $defaultLang = (string) config('brand.default_lang', 'es');

  if (preg_match('~^https?://masqueclima\.es(/[^?#]*)?(\?[^#]*)?(#.*)?$~i', $href, $m)) {
    $path = $m[1] ?? '/';
    $query = $m[2] ?? '';
    $fragment = $m[3] ?? '';

    $setLang = query_lang($query, $langs);
    if ($setLang !== null) {
      return localized_equivalent_url($currentPath, $setLang) . $fragment;
    }

    if ($path === '' || $path === '/') {
      $targetLang = $fragment !== '' ? $lang : $defaultLang;
      return '/' . rawurlencode($targetLang) . '/' . $fragment;
    }

    return $path . $fragment;
  }

  if (preg_match('~^\?setlang=([a-z]{2})(#.*)?$~i', $href, $m) && in_array($m[1], $langs, true)) {
    return localized_equivalent_url($currentPath, $m[1]) . ($m[2] ?? '');
  }

  if (preg_match('~^/\?setlang=([a-z]{2})(#.*)?$~i', $href, $m) && in_array($m[1], $langs, true)) {
    return localized_equivalent_url($currentPath, $m[1]) . ($m[2] ?? '');
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
  // sent=1 → success modal, sent=2 → spam/captcha error (also uses errorModal), sent=0 → error
  $modal = $sent === '1' ? 'thanksModal' : 'errorModal';
  return '<script>document.addEventListener("DOMContentLoaded",function(){var el=document.getElementById("' .
    $modal .
    '");if(el&&window.bootstrap){new bootstrap.Modal(el).show();}});</script>';
}

// ---------------------------------------------------------------------------
// LEGAL PAGES — render functions
// ---------------------------------------------------------------------------

function legal_pages(): array {
  static $cache = null;
  if ($cache !== null) {
    return $cache;
  }
  $file = __DIR__ . '/content/legal.php';
  $loaded = is_file($file) ? require $file : [];
  $cache = is_array($loaded) ? $loaded : [];
  return $cache;
}

function legal_page_for_path(string $path): ?array {
  $pages = legal_pages();
  return $pages[$path] ?? null;
}

/**
 * Cross-language hreflang equivalents for each legal page type.
 * Returns [lang => absoluteUrl, ...] for all 6 languages of a given type.
 */
function legal_hreflang_map(): array {
  static $map = null;
  if ($map !== null) {
    return $map;
  }
  $b = 'https://masqueclima.es';
  $map = [
    'cookies' => [
      'es' => $b . '/es/politica-de-cookies/',
      'en' => $b . '/en/cookie-policy/',
      'de' => $b . '/de/cookie-richtlinie/',
      'nl' => $b . '/nl/cookiebeleid/',
      'ru' => $b . '/ru/cookie-policy/',
      'no' => $b . '/no/cookie-policy/',
    ],
    'privacy' => [
      'es' => $b . '/es/politica-de-privacidad/',
      'en' => $b . '/en/privacy-policy/',
      'de' => $b . '/de/datenschutzerklaerung/',
      'nl' => $b . '/nl/privacybeleid/',
      'ru' => $b . '/ru/politika-konfidentsialnosti/',
      'no' => $b . '/no/personvernerklaering/',
    ],
    'legal' => [
      'es' => $b . '/es/aviso-legal/',
      'en' => $b . '/en/legal-notice/',
      'de' => $b . '/de/impressum/',
      'nl' => $b . '/nl/juridische-mededeling/',
      'ru' => $b . '/ru/pravovoe-uvedomlenie/',
      'no' => $b . '/no/juridisk-varsel/',
    ],
  ];
  return $map;
}

/**
 * Returns the localized cookie policy URL for a given language.
 */
function legal_cookie_policy_url(string $lang): string {
  $map = legal_hreflang_map()['cookies'];
  return $map[$lang] ?? $map['es'];
}

function legal_hreflang_html(string $type): string {
  $map = legal_hreflang_map();
  $langs = $map[$type] ?? [];
  $html = "\n<!-- Hreflang -->\n";
  foreach ($langs as $lang => $url) {
    $html .= '<link rel="alternate" hreflang="' . e($lang) . '" href="' . e($url) . '">' . "\n";
  }
  // x-default points to ES
  $xDefault = $langs['es'] ?? '';
  if ($xDefault !== '') {
    $html .= '<link rel="alternate" hreflang="x-default" href="' . e($xDefault) . '">' . "\n";
  }
  return $html;
}

function render_legal_page(string $path): ?string {
  $page = legal_page_for_path($path);
  if ($page === null) {
    return null;
  }
  $lang = (string) ($page['lang'] ?? 'es');
  $type = (string) ($page['type'] ?? 'cookies');
  $body = (string) ($page['body'] ?? '');

  return render_legal_shell($page, $body, $path, $lang, $type);
}

function render_legal_shell(array $page, string $body, string $path, string $lang, string $type): string {
  $homeSnapshotPath = '/' . rawurlencode($lang) . '/';
  $homeSnapshot = snapshot_file_for_path($homeSnapshotPath);

  // Fallback to ES home if lang snapshot missing
  if ($homeSnapshot === null && $lang !== 'es') {
    $homeSnapshot = snapshot_file_for_path('/es/');
  }

  if ($homeSnapshot === null) {
    return render_legal_minimal_shell($page, $body, $lang, $type);
  }

  $base = @file_get_contents($homeSnapshot);
  if ($base === false) {
    return render_legal_minimal_shell($page, $body, $lang, $type);
  }

  $mainOpen = '<main id="main-content">';
  $mainStart = strpos($base, $mainOpen);
  if ($mainStart === false) {
    return render_legal_minimal_shell($page, $body, $lang, $type);
  }

  $mainEnd = strpos($base, '</main>', $mainStart);
  if ($mainEnd === false) {
    return render_legal_minimal_shell($page, $body, $lang, $type);
  }

  $headAndHeader = substr($base, 0, $mainStart + strlen($mainOpen));
  $interactiveTail = legacy_es_interactive_main_tail($base, $mainStart + strlen($mainOpen), $mainEnd);
  $tail = substr($base, $mainEnd);

  $headAndHeader = patch_legal_head($headAndHeader, $page, $lang, $type);

  return $headAndHeader . "\n" . $body . "\n" . $interactiveTail . "\n" . $tail;
}

function render_legal_minimal_shell(array $page, string $body, string $lang, string $type): string {
  $canonical = (string) ($page['canonical'] ?? 'https://masqueclima.es/' . rawurlencode($lang) . '/');
  $hreflangHtml = legal_hreflang_html($type);
  return '<!DOCTYPE html><html lang="' . e($lang) . '"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
    . '<title>' . ($page['title'] ?? '') . '</title><meta name="description" content="' . ($page['description'] ?? '') . '">'
    . '<link rel="canonical" href="' . $canonical . '">'
    . $hreflangHtml
    . '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">'
    . '<link rel="stylesheet" href="/assets/css/styles.css"></head><body><main id="main-content">'
    . $body . '</main><script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>';
}

function patch_legal_head(string $html, array $page, string $lang, string $type): string {
  $canonical = (string) ($page['canonical'] ?? '');
  $title = (string) ($page['title'] ?? '');
  $description = (string) ($page['description'] ?? '');
  $hreflangHtml = legal_hreflang_html($type);

  $html = preg_replace('/<html\b[^>]*>/i', '<html lang="' . e($lang) . '">', $html, 1) ?? $html;
  $html = preg_replace('/<title>.*?<\/title>/is', '<title>' . $title . '</title>', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="description" content="[^"]*">/i', '<meta name="description" content="' . $description . '">', $html, 1) ?? $html;
  $html = preg_replace('/<link rel="canonical" href="[^"]*">/i', '<link rel="canonical" href="' . $canonical . '">', $html, 1) ?? $html;
  // Remove existing hreflang block and inject the legal one
  $html = preg_replace('/<!-- Hreflang -->\s*(?:<link rel="alternate"[^>]+>\s*)+/i', '', $html, 1) ?? $html;
  $html = preg_replace('/<meta property="og:title" content="[^"]*">/i', '<meta property="og:title" content="' . $title . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta property="og:description" content="[^"]*">/i', '<meta property="og:description" content="' . $description . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta property="og:url" content="[^"]*">/i', '<meta property="og:url" content="' . $canonical . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="twitter:title" content="[^"]*">/i', '<meta name="twitter:title" content="' . $title . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="twitter:description" content="[^"]*">/i', '<meta name="twitter:description" content="' . $description . '">', $html, 1) ?? $html;
  $html = str_replace('</head>', $hreflangHtml . '</head>', $html);

  return $html;
}

// ---------------------------------------------------------------------------
// LEGACY cookie policy render (kept for reference; actual rendering via render_legal_page)
// ---------------------------------------------------------------------------

function is_cookie_policy_path(string $path): bool {
  // Only matches the legacy no-lang path; the lang-prefixed path is handled by render_legal_page().
  return $path === '/politica-de-cookies/';
}

function cookie_policy_page_meta(): array {
  return [
    'title' => 'Pol&iacute;tica de cookies | +QUECLIMA',
    'description' => 'Informaci&oacute;n sobre el uso de cookies t&eacute;cnicas, de sesi&oacute;n y de terceros en la web de +QUECLIMA.',
    'canonical' => 'https://masqueclima.es/politica-de-cookies',
  ];
}

function render_cookie_policy_page(): string {
  $page = cookie_policy_page_meta();
  $body = cookie_policy_body();

  return render_cookie_policy_shell($page, $body);
}

function render_cookie_policy_shell(array $page, string $body): string {
  $homeSnapshot = snapshot_file_for_path('/es/');
  if ($homeSnapshot === null) {
    return render_cookie_policy_minimal_shell($page, $body);
  }

  $base = file_get_contents($homeSnapshot);
  if ($base === false) {
    return render_cookie_policy_minimal_shell($page, $body);
  }

  $mainOpen = '<main id="main-content">';
  $mainStart = strpos($base, $mainOpen);
  if ($mainStart === false) {
    return render_cookie_policy_minimal_shell($page, $body);
  }

  $mainEnd = strpos($base, '</main>', $mainStart);
  if ($mainEnd === false) {
    return render_cookie_policy_minimal_shell($page, $body);
  }

  $headAndHeader = substr($base, 0, $mainStart + strlen($mainOpen));
  $interactiveTail = legacy_es_interactive_main_tail($base, $mainStart + strlen($mainOpen), $mainEnd);
  $tail = substr($base, $mainEnd);
  $headAndHeader = patch_cookie_policy_head($headAndHeader, $page);

  return $headAndHeader . "\n" . $body . "\n" . $interactiveTail . "\n" . $tail;
}

function render_cookie_policy_minimal_shell(array $page, string $body): string {
  return '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
    . '<title>' . $page['title'] . '</title><meta name="description" content="' . $page['description'] . '">'
    . '<link rel="canonical" href="' . $page['canonical'] . '">'
    . '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">'
    . '<link rel="stylesheet" href="/assets/css/styles.css"></head><body><main id="main-content">'
    . $body . '</main><script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>';
}

function patch_cookie_policy_head(string $html, array $page): string {
  $html = preg_replace('/<html\b[^>]*>/i', '<html lang="es">', $html, 1) ?? $html;
  $html = preg_replace('/<title>.*?<\/title>/is', '<title>' . $page['title'] . '</title>', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="description" content="[^"]*">/i', '<meta name="description" content="' . $page['description'] . '">', $html, 1) ?? $html;
  $html = preg_replace('/<link rel="canonical" href="[^"]*">/i', '<link rel="canonical" href="' . $page['canonical'] . '">', $html, 1) ?? $html;
  $html = preg_replace('/<!-- Hreflang -->\s*(?:<link rel="alternate"[^>]+>\s*)+/i', '', $html, 1) ?? $html;
  $html = preg_replace('/<meta property="og:title" content="[^"]*">/i', '<meta property="og:title" content="' . $page['title'] . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta property="og:description" content="[^"]*">/i', '<meta property="og:description" content="' . $page['description'] . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta property="og:url" content="[^"]*">/i', '<meta property="og:url" content="' . $page['canonical'] . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="twitter:title" content="[^"]*">/i', '<meta name="twitter:title" content="' . $page['title'] . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="twitter:description" content="[^"]*">/i', '<meta name="twitter:description" content="' . $page['description'] . '">', $html, 1) ?? $html;

  return $html;
}

function cookie_policy_body(): string {
  return <<<HTML
<section class="legal-page" aria-labelledby="cookie-policy-title">
  <style>
    .legal-page{padding:7.5rem 0 3.5rem;background:#fff;}
    .legal-page .legal-wrap{max-width:880px;margin:0 auto;}
    .legal-page h1{font-size:2.35rem;line-height:1.16;margin:0 0 1rem;color:#102033;font-weight:900;}
    .legal-page h2{font-size:1.25rem;margin:2rem 0 .75rem;color:#142033;font-weight:800;}
    .legal-page p,.legal-page li{color:#425466;line-height:1.75;}
    .legal-page ul{padding-left:1.2rem;}
    .legal-page .legal-date{margin-top:2rem;color:#667789;font-size:.95rem;}
    @media (max-width:700px){.legal-page{padding:6.5rem 0 2.75rem}.legal-page h1{font-size:2rem}}
  </style>
  <div class="container">
    <div class="legal-wrap">
      <h1 id="cookie-policy-title">Pol&iacute;tica de cookies</h1>
      <p>Esta pol&iacute;tica explica de forma general c&oacute;mo puede utilizar cookies la web de +QUECLIMA y c&oacute;mo puedes gestionarlas desde tu navegador.</p>

      <h2>Qu&eacute; son las cookies</h2>
      <p>Las cookies son peque&ntilde;os archivos que una web puede guardar en el dispositivo del usuario para recordar informaci&oacute;n t&eacute;cnica, facilitar la navegaci&oacute;n o permitir determinadas funciones.</p>

      <h2>Qu&eacute; cookies puede usar esta web</h2>
      <p>La web puede utilizar cookies t&eacute;cnicas necesarias para su funcionamiento, cookies de sesi&oacute;n asociadas a formularios o preferencias b&aacute;sicas, y cookies de terceros cuando se cargan servicios externos.</p>

      <h2>Cookies t&eacute;cnicas necesarias</h2>
      <p>Estas cookies permiten que la web funcione correctamente, mantener una sesi&oacute;n temporal, recordar preferencias b&aacute;sicas como el idioma o proteger formularios frente a env&iacute;os no autorizados.</p>

      <h2>Cookies de sesi&oacute;n y formularios</h2>
      <p>Al usar formularios o ventanas de solicitud de presupuesto, la web puede generar identificadores temporales de sesi&oacute;n para validar el env&iacute;o y mejorar la seguridad. Estas cookies no se utilizan para elaborar perfiles comerciales.</p>

      <h2>Servicios de terceros</h2>
      <p>Algunas p&aacute;ginas pueden cargar contenidos o servicios de terceros, como mapas embebidos, anal&iacute;tica web o widgets externos. Estos proveedores pueden establecer sus propias cookies conforme a sus respectivas pol&iacute;ticas.</p>

      <h2>C&oacute;mo gestionar o bloquear cookies</h2>
      <p>Puedes permitir, bloquear o eliminar cookies desde la configuraci&oacute;n de tu navegador. Ten en cuenta que bloquear algunas cookies t&eacute;cnicas puede afectar al funcionamiento normal de la web o de sus formularios.</p>

      <h2>Contacto</h2>
      <p>Para cualquier consulta sobre esta pol&iacute;tica, puedes contactar con +QUECLIMA a trav&eacute;s de los medios de contacto disponibles en la web.</p>

      <p class="legal-date">Fecha de &uacute;ltima actualizaci&oacute;n: 28 de mayo de 2026.</p>
    </div>
  </div>
</section>
HTML;
}

function render_es_hub_page(string $path): ?string {
  $servicePage = es_service_page_for_path($path);
  if ($servicePage !== null) {
    return render_es_legacy_shell($servicePage, $path, render_service_detail_body($servicePage));
  }

  $guidePage = es_guide_page_for_path($path);
  if ($guidePage !== null) {
    return render_es_legacy_shell($guidePage, $path, render_guide_detail_body($guidePage, 'es'));
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
  $googleReviews = google_reviews_html('es');
  $finalCta = final_budget_cta_html('es');

  return $headAndHeader . "\n" . $body . "\n" . $googleReviews . "\n" . $finalCta . "\n" . $interactiveTail . "\n" . $tail;
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

function google_reviews_html(string $lang): string {
  return render_partial('google_reviews', ['lang' => $lang]);
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
  $serviceKey = null;
  foreach (localized_service_equivalent_paths() as $key => $paths) {
    if (($paths[$lang] ?? null) === ($service['path'] ?? '')) {
      $serviceKey = $key;
      break;
    }
  }
  $serviceGuideMap = [
    'installation' => 'capacity',
    'maintenance'  => 'maintenance',
    'repair'       => 'maintenance',
    'heat_pump'    => 'heat_pump',
    'solar_thermal'=> 'heat_pump',
  ];
  $relatedGuideUrl = null;
  $relatedGuideTitle = null;
  if ($serviceKey !== null) {
    $guideKey = $serviceGuideMap[$serviceKey] ?? null;
    if ($guideKey !== null) {
      $guidePaths = localized_guide_equivalent_paths();
      $relatedGuideUrl = $guidePaths[$guideKey][$lang] ?? null;
      $guideFiles = [
        'es' => __DIR__ . '/content/guides/es.php',
        'en' => __DIR__ . '/content/guides/en.php',
        'de' => __DIR__ . '/content/guides/de.php',
        'nl' => __DIR__ . '/content/guides/nl.php',
        'ru' => __DIR__ . '/content/guides/ru.php',
        'no' => __DIR__ . '/content/guides/no.php',
      ];
      $guideData = [];
      if (isset($guideFiles[$lang]) && is_file($guideFiles[$lang])) {
        $loadedGuides = require $guideFiles[$lang];
        if (is_array($loadedGuides) && isset($guidePaths[$guideKey][$lang])) {
          $guideData = $loadedGuides[$guidePaths[$guideKey][$lang]] ?? [];
        }
      }
      $relatedGuideTitle = (string)($guideData['h1'] ?? $guideData['title'] ?? '');
    }
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
  $guidesUrl  = localized_hub_url($lang, 'guides');
  $guidesLabel = match ($lang) {
    'en'    => 'Guides',
    'de'    => 'Ratgeber',
    'nl'    => 'Gidsen',
    'ru'    => '&#1043;&#1080;&#1076;&#1099;',
    'no'    => 'Guider',
    default => 'Gu&iacute;as de climatizaci&oacute;n',
  };
  $relatedGuideLabel = match ($lang) {
    'en'    => 'Related guide',
    'de'    => 'Verwandter Ratgeber',
    'nl'    => 'Gerelateerde gids',
    'ru'    => '&#1057;&#1074;&#1103;&#1079;&#1072;&#1085;&#1085;&#1086;&#1077; &#1088;&#1091;&#1082;&#1086;&#1074;&#1086;&#1076;&#1089;&#1090;&#1074;&#1086;',
    'no'    => 'Relatert guide',
    default => 'Guía relacionada',
  };
  ob_start();
  include __DIR__ . '/../views/service_detail.php';
  return (string) ob_get_clean();
}

function render_es_minimal_shell(array $page, string $path, string $body): string {
  $canonical = 'https://masqueclima.es' . $path;
  $hreflang = page_hreflang_html($path, $page, 'es');
  return '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">' .
    '<title>' . $page['title'] . '</title><meta name="description" content="' . $page['description'] . '">' .
    '<link rel="canonical" href="' . $canonical . '"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">' .
    '<link rel="stylesheet" href="/assets/css/styles.css">' . $hreflang . es_page_jsonld($path, $page) . '</head><body><main id="main-content">' .
    $body . google_reviews_html('es') . final_budget_cta_html('es') . '</main><script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>';
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
  if (!empty($page['hero_image'])) {
    $html = patch_og_image_meta($html, 'https://masqueclima.es' . $page['hero_image']);
  }
  $hreflang = page_hreflang_html($path, $page, 'es');
  $html = str_replace('</head>', $hreflang . es_page_jsonld($path, $page) . "\n</head>", $html);

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

  if (str_starts_with($path, '/es/blog/') && $path !== '/es/blog/') {
    $itemList[] = [
      '@type' => 'ListItem',
      'position' => 2,
      'name' => 'Guías',
      'item' => $base . '/es/blog/',
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

  if (!empty($page['article_type']) && $page['article_type'] === 'BlogPosting') {
    $orgUrl = $base . '/es/';
    $blogPosting = [
      '@context'      => 'https://schema.org',
      '@type'         => 'BlogPosting',
      'headline'      => html_entity_decode((string)($page['h1'] ?? $page['title']), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
      'description'   => html_entity_decode((string)($page['description'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
      'url'           => $base . $path,
      'datePublished' => (string)($page['date_published'] ?? ''),
      'dateModified'  => (string)($page['date_modified'] ?? ''),
      'inLanguage'    => 'es',
      'author'        => ['@type' => 'Organization', 'name' => '+QUECLIMA', 'url' => $orgUrl],
      'publisher'     => ['@type' => 'Organization', 'name' => '+QUECLIMA', 'url' => $orgUrl],
    ];
    if (!empty($page['og_image'])) {
      $blogPosting['image'] = $base . $page['og_image'];
    }
    $schemas[] = $blogPosting;
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

  $svcPaths = localized_service_equivalent_paths();
  $svcLabels = locality_service_link_labels()['es'];
  $esServicePills = '';
  foreach (['installation', 'maintenance', 'repair', 'heat_pump', 'solar_thermal'] as $key) {
    $esServicePills .= '<a class="hub-pill" href="' . $svcPaths[$key]['es'] . '">' . $svcLabels[$key] . '</a>' . "\n";
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
<section class="hub-section" aria-labelledby="zonas-servicios">
  <div class="container">
    <p class="hub-kicker">Servicios</p>
    <h2 class="section-title" id="zonas-servicios">Servicios de climatizaci&oacute;n disponibles</h2>
    <div class="hub-links">
      {$esServicePills}
      <a class="hub-pill" href="/es/servicios/">Todos los servicios</a>
    </div>
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

function locality_display_names(): array {
  return [
    'albir'              => 'Albir',
    'alfaz-del-pi'       => 'Alfaz del Pi',
    'altea'              => 'Altea',
    'beniarda'           => 'Beniard&agrave;',
    'benidorm'           => 'Benidorm',
    'benifato'           => 'Benifato',
    'benimantell'        => 'Benimantell',
    'bolulla'            => 'Bolulla',
    'callosa-den-sarria' => 'Callosa d&rsquo;en Sarri&agrave;',
    'calpe'              => 'Calpe',
    'confrides'          => 'Confrides',
    'finestrat'          => 'Finestrat',
    'guadalest'          => 'Guadalest',
    'la-nucia'           => 'La Nuc&iacute;a',
    'orxeta'             => 'Orxeta',
    'polop'              => 'Polop',
    'relleu'             => 'Relleu',
    'sella'              => 'Sella',
    'tarbena'            => 'T&agrave;rbena',
    'villajoyosa'        => 'Villajoyosa',
  ];
}

function locality_service_link_labels(): array {
  return [
    'es' => [
      'installation'  => 'Instalaci&oacute;n de aire acondicionado',
      'maintenance'   => 'Mantenimiento de climatizaci&oacute;n',
      'repair'        => 'Reparaci&oacute;n de aire acondicionado',
      'heat_pump'     => 'Aerotermia y bomba de calor',
      'solar_thermal' => 'Energ&iacute;a solar t&eacute;rmica',
    ],
    'en' => [
      'installation'  => 'Air conditioning installation',
      'maintenance'   => 'Climate control maintenance',
      'repair'        => 'Air conditioning repair',
      'heat_pump'     => 'Heat pump &amp; aerothermal',
      'solar_thermal' => 'Solar thermal energy',
    ],
    'de' => [
      'installation'  => 'Klimaanlagen-Installation',
      'maintenance'   => 'Klimaanlagen-Wartung',
      'repair'        => 'Klimaanlagen-Reparatur',
      'heat_pump'     => 'W&auml;rmepumpe &amp; Aerothermie',
      'solar_thermal' => 'Solarthermie',
    ],
    'nl' => [
      'installation'  => 'Airco-installatie',
      'maintenance'   => 'Klimaatbeheersing-onderhoud',
      'repair'        => 'Airco-reparatie',
      'heat_pump'     => 'Warmtepomp &amp; aerothermie',
      'solar_thermal' => 'Zonneboiler &amp; zonne-energie',
    ],
    'ru' => [
      'installation'  => '&#1059;&#1089;&#1090;&#1072;&#1085;&#1086;&#1074;&#1082;&#1072; &#1082;&#1086;&#1085;&#1076;&#1080;&#1094;&#1080;&#1086;&#1085;&#1077;&#1088;&#1072;',
      'maintenance'   => '&#1054;&#1073;&#1089;&#1083;&#1091;&#1078;&#1080;&#1074;&#1072;&#1085;&#1080;&#1077; &#1082;&#1086;&#1085;&#1076;&#1080;&#1094;&#1080;&#1086;&#1085;&#1077;&#1088;&#1086;&#1074;',
      'repair'        => '&#1056;&#1077;&#1084;&#1086;&#1085;&#1090; &#1082;&#1086;&#1085;&#1076;&#1080;&#1094;&#1080;&#1086;&#1085;&#1077;&#1088;&#1086;&#1074;',
      'heat_pump'     => '&#1058;&#1077;&#1087;&#1083;&#1086;&#1074;&#1086;&#1081; &#1085;&#1072;&#1089;&#1086;&#1089;',
      'solar_thermal' => '&#1057;&#1086;&#1083;&#1085;&#1077;&#1095;&#1085;&#1099;&#1077; &#1082;&#1086;&#1083;&#1083;&#1077;&#1082;&#1090;&#1086;&#1088;&#1099;',
    ],
    'no' => [
      'installation'  => 'Installasjon av aircondition',
      'maintenance'   => 'Vedlikehold av klimaanlegg',
      'repair'        => 'Reparasjon av aircondition',
      'heat_pump'     => 'Varmepumpe &amp; aerotermi',
      'solar_thermal' => 'Solvarme',
    ],
  ];
}

function patch_nonES_locality_seo(string $html, string $path, string $lang): string {
  if ($lang === 'es') {
    return $html;
  }

  $patterns = [
    'en' => '~^/en/air-conditioning-([a-z0-9-]+)/$~',
    'de' => '~^/de/klimaanlage-([a-z0-9-]+)/$~',
    'nl' => '~^/nl/airco-([a-z0-9-]+)/$~',
    'ru' => '~^/ru/konditsioner-([a-z0-9-]+)/$~',
    'no' => '~^/no/aircondition-([a-z0-9-]+)/$~',
  ];

  $pattern = $patterns[$lang] ?? null;
  if ($pattern === null || !preg_match($pattern, $path, $m)) {
    return $html;
  }

  $slug  = $m[1];
  $names = locality_display_names();
  $city  = $names[$slug] ?? ucfirst(str_replace('-', ' ', $slug));

  $titles = [
    'en' => 'Air conditioning in %s | +QUECLIMA',
    'de' => 'Klimaanlage in %s | +QUECLIMA',
    'nl' => 'Airco in %s | +QUECLIMA',
    'ru' => '&#1050;&#1086;&#1085;&#1076;&#1080;&#1094;&#1080;&#1086;&#1085;&#1077;&#1088; &#1074; %s | +QUECLIMA',
    'no' => 'Aircondition i %s | +QUECLIMA',
  ];

  $metas = [
    'en' => 'Air conditioning installation, maintenance and repair in %s and the Marina Baixa. Clear advice for homes, businesses and holiday apartments.',
    'de' => 'Installation, Wartung und Reparatur von Klimaanlagen in %s und der Marina Baixa. Klare Beratung f&uuml;r Wohnungen, H&auml;user und Gesch&auml;fte.',
    'nl' => 'Installatie, onderhoud en reparatie van airconditioning in %s en de Marina Baixa. Duidelijk advies voor woningen, bedrijven en vakantieappartementen.',
    'ru' => '&#1059;&#1089;&#1090;&#1072;&#1085;&#1086;&#1074;&#1082;&#1072;, &#1086;&#1073;&#1089;&#1083;&#1091;&#1078;&#1080;&#1074;&#1072;&#1085;&#1080;&#1077; &#1080; &#1088;&#1077;&#1084;&#1086;&#1085;&#1090; &#1082;&#1086;&#1085;&#1076;&#1080;&#1094;&#1080;&#1086;&#1085;&#1077;&#1088;&#1086;&#1074; &#1074; %s &#1080; Marina Baixa. &#1055;&#1086;&#1085;&#1103;&#1090;&#1085;&#1072;&#1103; &#1082;&#1086;&#1085;&#1089;&#1091;&#1083;&#1100;&#1090;&#1072;&#1094;&#1080;&#1103; &#1076;&#1083;&#1103; &#1076;&#1086;&#1084;&#1086;&#1074;, &#1082;&#1074;&#1072;&#1088;&#1090;&#1080;&#1088; &#1080; &#1082;&#1086;&#1084;&#1084;&#1077;&#1088;&#1095;&#1077;&#1089;&#1082;&#1080;&#1093; &#1087;&#1086;&#1084;&#1077;&#1097;&#1077;&#1085;&#1080;&#1081;.',
    'no' => 'Installasjon, vedlikehold og reparasjon av aircondition i %s og Marina Baixa. Tydelige r&aring;d for boliger, bedrifter og ferieboliger.',
  ];

  $title     = sprintf($titles[$lang], $city);
  $meta      = sprintf($metas[$lang], $city);
  $canonical = 'https://masqueclima.es' . $path;

  $html = patch_snapshot_seo_meta($html, $title, $meta, $canonical);

  if (!str_contains($html, 'locality-links-block')) {
    $block = build_locality_links_block($lang, $slug, $city);
    $needle = '<section class="zona" id="zona">';
    if (str_contains($html, $needle)) {
      $html = str_replace($needle, $block . "\n" . $needle, $html);
    } else {
      $html = str_replace('</main>', $block . "\n</main>", $html);
    }
  }

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

  if (!str_contains($html, 'p1-internal-links')) {
    $html = insert_p1_internal_links($html, $page);
  }

  return $html;
}

function patch_og_image_meta(string $html, string $absoluteUrl): string {
  $html = preg_replace('/<meta property="og:image" content="[^"]*">/i', '<meta property="og:image" content="' . $absoluteUrl . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="twitter:image" content="[^"]*">/i', '<meta name="twitter:image" content="' . $absoluteUrl . '">', $html, 1) ?? $html;
  return $html;
}

function patch_snapshot_og_image(string $html, string $path, string $lang): string {
  $patterns = [
    'es' => '~^/es/aire-acondicionado-([a-z0-9-]+)/$~',
    'en' => '~^/en/air-conditioning-([a-z0-9-]+)/$~',
    'de' => '~^/de/klimaanlage-([a-z0-9-]+)/$~',
    'nl' => '~^/nl/airco-([a-z0-9-]+)/$~',
    'ru' => '~^/ru/konditsioner-([a-z0-9-]+)/$~',
    'no' => '~^/no/aircondition-([a-z0-9-]+)/$~',
  ];
  $pattern = $patterns[$lang] ?? null;
  if ($pattern === null || !preg_match($pattern, $path, $m)) {
    return $html;
  }
  $heroMap = locality_hero_map();
  $slug    = $m[1];
  if (!isset($heroMap[$slug])) {
    return $html;
  }
  return patch_og_image_meta($html, 'https://masqueclima.es' . $heroMap[$slug]);
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
      <a class="p1-pill" href="/es/servicios/instalacion-aire-acondicionado/">Instalaci&oacute;n de aire acondicionado</a>
      <a class="p1-pill" href="/es/servicios/mantenimiento-climatizacion/">Mantenimiento de climatizaci&oacute;n</a>
      <a class="p1-pill" href="/es/servicios/reparacion-aire-acondicionado/">Reparaci&oacute;n de aire acondicionado</a>
      <a class="p1-pill" href="/es/servicios/aerotermia-bomba-calor/">Aerotermia y bomba de calor</a>
      <a class="p1-pill" href="/es/servicios/energia-solar-termica/">Energ&iacute;a solar t&eacute;rmica</a>
      <a class="p1-pill" href="/es/servicios/">Todos los servicios</a>
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
// NON-ES LOCALITY LINKS BLOCK
// ──────────────────────────────────────────────────

function build_locality_links_block(string $lang, string $slug, string $city): string {
  $serviceUrls    = localized_service_equivalent_paths();
  $svcLabelMap    = locality_service_link_labels();
  $serviceLabels  = $svcLabelMap[$lang] ?? $svcLabelMap['en'];
  $urlPrefix      = localized_locality_prefixes()[$lang] ?? '/es/aire-acondicionado-';
  $servicesHubUrl = e(localized_hub_url($lang, 'services') ?? '#');
  $zonesHubUrl    = e(localized_hub_url($lang, 'zones') ?? '#');
  $displayNames   = locality_display_names();

  $servicePills = '';
  foreach (['installation', 'maintenance', 'repair', 'heat_pump', 'solar_thermal'] as $key) {
    $url   = e($serviceUrls[$key][$lang] ?? '#');
    $label = $serviceLabels[$key] ?? $key;
    $servicePills .= '<a class="loc-pill" href="' . $url . '">' . $label . '</a>' . "\n";
  }

  $nearbyPills = '';
  foreach (array_slice(nearby_locality_slugs($slug), 0, 3) as $nearbySlug) {
    $url  = e($urlPrefix . $nearbySlug . '/');
    $name = $displayNames[$nearbySlug] ?? ucwords(str_replace('-', ' ', $nearbySlug));
    $nearbyPills .= '<a class="loc-pill" href="' . $url . '">' . $name . '</a>' . "\n";
  }

  $sectionTitle = match ($lang) {
    'en'    => 'Climate services in ' . $city,
    'de'    => 'Klimadienste in ' . $city,
    'nl'    => 'Klimaatdiensten in ' . $city,
    'ru'    => '&#1059;&#1089;&#1083;&#1091;&#1075;&#1080; &#1074; ' . $city,
    'no'    => 'Klimatjenester i ' . $city,
    default => 'Servicios de climatizaci&oacute;n en ' . $city,
  };
  $nearbyTitle = match ($lang) {
    'en'    => 'Nearby areas',
    'de'    => 'Benachbarte Orte',
    'nl'    => 'Nabijgelegen gebieden',
    'ru'    => '&#1057;&#1086;&#1089;&#1077;&#1076;&#1085;&#1080;&#1077; &#1088;&#1072;&#1081;&#1086;&#1085;&#1099;',
    'no'    => 'Naboromr&aring;der',
    default => 'Localidades cercanas',
  };
  $allZonesLabel = match ($lang) {
    'en'    => 'All areas',
    'de'    => 'Alle Gebiete',
    'nl'    => 'Alle gebieden',
    'ru'    => '&#1042;&#1089;&#1077; &#1088;&#1072;&#1081;&#1086;&#1085;&#1099;',
    'no'    => 'Alle omr&aring;der',
    default => 'Todas las zonas',
  };
  $ctaLabel = match ($lang) {
    'en'    => 'Get a quote',
    'de'    => 'Angebot anfordern',
    'nl'    => 'Offerte aanvragen',
    'ru'    => '&#1047;&#1072;&#1087;&#1088;&#1086;&#1089;&#1080;&#1090;&#1100; &#1087;&#1088;&#1077;&#1076;&#1083;&#1086;&#1078;&#1077;&#1085;&#1080;&#1077;',
    'no'    => 'F&aring; tilbud',
    default => 'Pedir presupuesto',
  };

  return <<<HTML
<section class="container py-4 locality-links-block" id="locality-servicios">
  <style>
    .locality-links-block{border-top:1px solid #edf2f7;border-bottom:1px solid #edf2f7;}
    .locality-links-block .loc-box{background:#f7faff;border:1px solid #e4edf8;border-radius:14px;padding:22px;}
    .locality-links-block h2{font-size:1.25rem;font-weight:800;color:#142033;margin-bottom:.7rem;}
    .locality-links-block h3{font-size:1rem;font-weight:700;color:#425466;margin:.8rem 0 .4rem;}
    .loc-pill{display:inline-flex;margin:.25rem .35rem .25rem 0;border:1px solid #d5e3f2;border-radius:999px;padding:.42rem .7rem;background:#fff;color:#12324f;font-weight:700;text-decoration:none;}
    .loc-pill:hover{border-color:#0074e8;color:#0074e8;background:#fff;}
  </style>
  <div class="loc-box">
    <h2>{$sectionTitle}</h2>
    <div class="mb-2">
      {$servicePills}
    </div>
    <h3>{$nearbyTitle}</h3>
    <div class="mb-3">
      <a class="loc-pill" href="{$zonesHubUrl}">{$allZonesLabel}</a>
      {$nearbyPills}
    </div>
    <a class="btn btn-primary js-track" data-ev="cta_quote_locality" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote" aria-controls="quoteModal">{$ctaLabel}</a>
  </div>
</section>
HTML;
}

// ──────────────────────────────────────────────────
// MULTILINGUAL HUB + SERVICE PAGE RENDERING
// ──────────────────────────────────────────────────

function render_lang_hub_page(string $path, string $lang): ?string {
  $servicePage = lang_service_page_for_path($path, $lang);
  if ($servicePage !== null) {
    return render_lang_legacy_shell($servicePage, $path, render_service_detail_body($servicePage, $lang), $lang);
  }

  $guidePage = lang_guide_page_for_path($path, $lang);
  if ($guidePage !== null) {
    return render_lang_legacy_shell($guidePage, $path, render_guide_detail_body($guidePage, $lang), $lang);
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
  $googleReviews = google_reviews_html($lang);
  $finalCta = final_budget_cta_html($lang);

  return $headAndHeader . "\n" . $body . "\n" . $googleReviews . "\n" . $finalCta . "\n" . $interactiveTail . "\n" . $tail;
}

function render_lang_minimal_shell(array $page, string $path, string $body, string $lang): string {
  $canonical = 'https://masqueclima.es' . $path;
  $hreflang = page_hreflang_html($path, $page, $lang);
  return '<!DOCTYPE html><html lang="' . e($lang) . '"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
    . '<title>' . $page['title'] . '</title><meta name="description" content="' . $page['description'] . '">'
    . '<link rel="canonical" href="' . $canonical . '"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">'
    . '<link rel="stylesheet" href="/assets/css/styles.css">' . $hreflang . lang_page_jsonld($path, $lang, $page) . '</head><body><main id="main-content">'
    . $body . google_reviews_html($lang) . final_budget_cta_html($lang) . '</main><script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>';
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
  if (!empty($page['hero_image'])) {
    $html = patch_og_image_meta($html, 'https://masqueclima.es' . $page['hero_image']);
  }
  $hreflang = page_hreflang_html($path, $page, $lang);
  $html = str_replace('</head>', $hreflang . lang_page_jsonld($path, $lang, $page) . "\n</head>", $html);

  return $html;
}

function page_hreflang_html(string $path, array $page, string $lang): string {
  if (($page['hreflang'] ?? null) === 'es_only') {
    return '';
  }

  if (!empty($page['guide_key'])) {
    return guide_hreflang_html((string) $page['guide_key']);
  }

  return localized_hreflang_links_html($path);
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

  $guidesHubUrl = localized_hub_url($lang, 'guides');
  $guidesLabel = match ($lang) {
    'en'    => 'Guides',
    'de'    => 'Ratgeber',
    'nl'    => 'Gidsen',
    'ru'    => 'Руководства',
    'no'    => 'Guider',
    default => 'Guías',
  };
  if ($guidesHubUrl !== null && str_starts_with($path, $guidesHubUrl) && $path !== $guidesHubUrl) {
    $itemList[] = [
      '@type' => 'ListItem',
      'position' => 2,
      'name' => $guidesLabel,
      'item' => $base . $guidesHubUrl,
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

  if (!empty($page['article_type']) && $page['article_type'] === 'BlogPosting') {
    $orgUrl = $base . '/es/';
    $blogPosting = [
      '@context'      => 'https://schema.org',
      '@type'         => 'BlogPosting',
      'headline'      => html_entity_decode((string)($page['h1'] ?? $page['title']), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
      'description'   => html_entity_decode((string)($page['description'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
      'url'           => $base . $path,
      'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id'   => $base . $path,
      ],
      'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id'   => $base . $path,
      ],
      'datePublished' => (string)($page['date_published'] ?? ''),
      'dateModified'  => (string)($page['date_modified'] ?? ''),
      'inLanguage'    => $lang,
      'author'        => ['@type' => 'Organization', 'name' => '+QUECLIMA', 'url' => $orgUrl],
      'publisher'     => ['@type' => 'Organization', 'name' => '+QUECLIMA', 'url' => $orgUrl],
    ];
    if (!empty($page['og_image'])) {
      $blogPosting['image'] = $base . $page['og_image'];
    }
    $schemas[] = $blogPosting;
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

  // render localized guide cards (first 3 guides)
  $guideCards = render_partial('guide_cards');

  // render localized guide cards (first 3 guides)
  $guideCards = render_partial('guide_cards');
  $serviceCards = lang_service_cards_html($lang);
  $miniMap = render_partial('zone_visual', ['variant' => 'mini']);
  if ($lang !== 'es') {
    $langPrefix = match ($lang) {
      'en'    => '/en/air-conditioning-',
      'de'    => '/de/klimaanlage-',
      'nl'    => '/nl/airco-',
      'ru'    => '/ru/konditsioner-',
      'no'    => '/no/aircondition-',
      default => '/es/aire-acondicionado-',
    };
    $miniMap = str_replace('href="/es/aire-acondicionado-', 'href="' . $langPrefix, $miniMap);
  }
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
  if ($lang !== 'es') {
    $langPrefix = match ($lang) {
      'en'    => '/en/air-conditioning-',
      'de'    => '/de/klimaanlage-',
      'nl'    => '/nl/airco-',
      'ru'    => '/ru/konditsioner-',
      'no'    => '/no/aircondition-',
      default => '/es/aire-acondicionado-',
    };
    $zoneMap = str_replace('href="/es/aire-acondicionado-', 'href="' . $langPrefix, $zoneMap);
  }
  $allPills = lang_all_locality_pills_html($lang);

  $svcUrls2       = localized_service_equivalent_paths();
  $svcLabelMap2   = locality_service_link_labels();
  $svcLabels2     = $svcLabelMap2[$lang] ?? $svcLabelMap2['en'];
  $svcHubUrl2     = e(localized_hub_url($lang, 'services') ?? '#');
  $langServicePills = '';
  foreach (['installation', 'maintenance', 'repair', 'heat_pump', 'solar_thermal'] as $key) {
    $url   = e($svcUrls2[$key][$lang] ?? '#');
    $label = $svcLabels2[$key] ?? $key;
    $langServicePills .= '<a class="hub-pill" href="' . $url . '">' . $label . '</a>' . "\n";
  }
  $svcSectionKicker = match ($lang) {
    'en'    => 'Services',
    'de'    => 'Leistungen',
    'nl'    => 'Diensten',
    'ru'    => '&#1059;&#1089;&#1083;&#1091;&#1075;&#1080;',
    'no'    => 'Tjenester',
    default => 'Servicios',
  };
  $svcSectionH2 = match ($lang) {
    'en'    => 'Services available in these areas',
    'de'    => 'Verf&uuml;gbare Leistungen in diesen Gebieten',
    'nl'    => 'Beschikbare diensten in deze gebieden',
    'ru'    => '&#1044;&#1086;&#1089;&#1090;&#1091;&#1087;&#1085;&#1099;&#1077; &#1091;&#1089;&#1083;&#1091;&#1075;&#1080; &#1074; &#1101;&#1090;&#1080;&#1093; &#1088;&#1072;&#1081;&#1086;&#1085;&#1072;&#1093;',
    'no'    => 'Tilgjengelige tjenester i disse omr&aring;dene',
    default => 'Servicios disponibles en estas zonas',
  };
  $allServicesLabelAreas = match ($lang) {
    'en'    => 'All services',
    'de'    => 'Alle Leistungen',
    'nl'    => 'Alle diensten',
    'ru'    => '&#1042;&#1089;&#1077; &#1091;&#1089;&#1083;&#1091;&#1075;&#1080;',
    'no'    => 'Alle tjenester',
    default => 'Todos los servicios',
  };

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
<section class="hub-section" aria-labelledby="lang-areas-services">
  <div class="container">
    <p class="hub-kicker">{$svcSectionKicker}</p>
    <h2 class="section-title" id="lang-areas-services">{$svcSectionH2}</h2>
    <div class="hub-links">
      {$langServicePills}
      <a class="hub-pill" href="{$svcHubUrl2}">{$allServicesLabelAreas}</a>
    </div>
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
  $guideCards = render_partial('guide_cards');

  $svcUrls3       = localized_service_equivalent_paths();
  $svcLabelMap3   = locality_service_link_labels();
  $svcLabels3     = $svcLabelMap3[$lang] ?? $svcLabelMap3['en'];
  $guidesSvcHubUrl = e(localized_hub_url($lang, 'services') ?? '#');
  $guidesSvcPills = '';
  foreach (['installation', 'maintenance', 'repair', 'heat_pump', 'solar_thermal'] as $key) {
    $url   = e($svcUrls3[$key][$lang] ?? '#');
    $label = $svcLabels3[$key] ?? $key;
    $guidesSvcPills .= '<a class="hub-pill" href="' . $url . '">' . $label . '</a>' . "\n";
  }
  $guidesRelatedKicker = match ($lang) {
    'en'    => 'Related',
    'de'    => 'Verwandt',
    'nl'    => 'Gerelateerd',
    'ru'    => '&#1057;&#1074;&#1103;&#1079;&#1072;&#1085;&#1085;&#1099;&#1077;',
    'no'    => 'Relatert',
    default => 'Relacionado',
  };
  $guidesRelatedH2 = match ($lang) {
    'en'    => 'Related services',
    'de'    => 'Verwandte Leistungen',
    'nl'    => 'Gerelateerde diensten',
    'ru'    => '&#1057;&#1074;&#1103;&#1079;&#1072;&#1085;&#1085;&#1099;&#1077; &#1091;&#1089;&#1083;&#1091;&#1075;&#1080;',
    'no'    => 'Relaterte tjenester',
    default => 'Servicios relacionados',
  };
  $guidesRelatedP = match ($lang) {
    'en'    => 'Discover our climate control services for homes and businesses on the Costa Blanca.',
    'de'    => 'Entdecken Sie unsere Klimaanlagen-Leistungen f&uuml;r Haushalte und Unternehmen an der Costa Blanca.',
    'nl'    => 'Ontdek onze klimaatdiensten voor woningen en bedrijven aan de Costa Blanca.',
    'ru'    => '&#1054;&#1090;&#1082;&#1088;&#1086;&#1081;&#1090;&#1077; &#1085;&#1072;&#1096;&#1080; &#1091;&#1089;&#1083;&#1091;&#1075;&#1080; &#1082;&#1083;&#1080;&#1084;&#1072;&#1090;&#1080;&#1079;&#1072;&#1094;&#1080;&#1080; &#1076;&#1083;&#1103; &#1076;&#1086;&#1084;&#1086;&#1074; &#1080; &#1087;&#1088;&#1077;&#1076;&#1087;&#1088;&#1080;&#1103;&#1090;&#1080;&#1081; &#1085;&#1072; Costa Blanca.',
    'no'    => 'Utforsk v&aring;re klimatjenester for boliger og bedrifter p&aring; Costa Blanca.',
    default => 'Descubre nuestros servicios de climatizaci&oacute;n para viviendas y negocios en la Costa Blanca.',
  };
  $guidesAllServicesLabel = match ($lang) {
    'en'    => 'All services',
    'de'    => 'Alle Leistungen',
    'nl'    => 'Alle diensten',
    'ru'    => '&#1042;&#1089;&#1077; &#1091;&#1089;&#1083;&#1091;&#1075;&#1080;',
    'no'    => 'Alle tjenester',
    default => 'Todos los servicios',
  };

  return <<<HTML
{$styles}
{$intro}
<section class="hub-section" aria-labelledby="lang-guides-list">
  <div class="container">
    <p class="hub-kicker">{$kicker}</p>
    <h2 class="section-title" id="lang-guides-list">{$h2}</h2>
    <p class="hub-muted">{$p}</p>
    <div class="guide-cards">{$guideCards}</div>
  </div>
</section>
<section class="hub-section alt" aria-labelledby="lang-guides-services">
  <div class="container">
    <p class="hub-kicker">{$guidesRelatedKicker}</p>
    <h2 class="section-title" id="lang-guides-services">{$guidesRelatedH2}</h2>
    <p class="hub-muted">{$guidesRelatedP}</p>
    <div class="hub-links">
      {$guidesSvcPills}
      <a class="hub-pill" href="{$guidesSvcHubUrl}">{$guidesAllServicesLabel}</a>
    </div>
  </div>
</section>
HTML;
}

function lang_service_cards_html(string $lang): string {
  $services = lang_service_pages($lang);
  if (empty($services)) {
    return '';
  }
  $ctaLabels = [
    'en' => 'View service',
    'de' => 'Leistung ansehen',
    'nl' => 'Dienst bekijken',
    'ru' => '&#1055;&#1086;&#1089;&#1084;&#1086;&#1090;&#1088;&#1077;&#1090;&#1100; &#1091;&#1089;&#1083;&#1091;&#1075;&#1091;',
    'no' => 'Se tjeneste',
  ];
  $ctaLabel = $ctaLabels[$lang] ?? 'Ver servicio';
  $html = '<div class="hub-grid services-grid">' . "\n";
  foreach ($services as $path => $s) {
    $title = e($s['h1'] ?? $s['title'] ?? '');
    $desc  = e($s['subtitle'] ?? $s['description'] ?? '');
    $url   = e($path);
    $html .= '  <article class="hub-card">'
      . '<h3>' . $title . '</h3>'
      . '<p>' . $desc . '</p>'
      . '<a class="btn btn-primary js-track" data-ev="nav_service_lang" href="' . $url . '">' . $ctaLabel . '</a>'
      . '</article>' . "\n";
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

// ── Guide page helpers ────────────────────────────────────────────────

function guide_hreflang_map(): array {
  static $map = null;
  if ($map !== null) return $map;
  $b = 'https://masqueclima.es';
  $map = [
    'install_cost_benidorm' => [
      'es' => $b . '/es/blog/cuanto-cuesta-instalar-aire-acondicionado-benidorm/',
      'en' => $b . '/en/guides/how-much-air-conditioning-installation-costs-benidorm/',
      'de' => $b . '/de/ratgeber/kosten-klimaanlage-installation-benidorm/',
      'nl' => $b . '/nl/gidsen/kosten-airco-installatie-benidorm/',
      'ru' => $b . '/ru/gidy/skolko-stoit-ustanovit-konditsioner-benidorm/',
      'no' => $b . '/no/guider/hva-koster-installasjon-aircondition-benidorm/',
    ],
    'not_cooling' => [
      'es' => $b . '/es/blog/por-que-aire-acondicionado-no-enfria/',
      'en' => $b . '/en/guides/why-air-conditioning-not-cooling/',
      'de' => $b . '/de/ratgeber/warum-kuehlt-klimaanlage-nicht/',
      'nl' => $b . '/nl/gidsen/waarom-koelt-airco-niet/',
      'ru' => $b . '/ru/gidy/pochemu-konditsioner-ne-ohlazhdaet/',
      'no' => $b . '/no/guider/hvorfor-kjoler-ikke-aircondition/',
    ],
    'ducted_vs_split' => [
      'es' => $b . '/es/blog/aire-acondicionado-conductos-o-split/',
      'en' => $b . '/en/guides/ducted-air-conditioning-or-split/',
      'de' => $b . '/de/ratgeber/kanal-klimaanlage-oder-split/',
      'nl' => $b . '/nl/gidsen/kanaalairco-of-split/',
      'ru' => $b . '/ru/gidy/kanalnyj-konditsioner-ili-split/',
      'no' => $b . '/no/guider/kanalbasert-aircondition-eller-split/',
    ],
    'holiday_apartments' => [
      'es' => $b . '/es/blog/aire-acondicionado-apartamentos-turisticos-benidorm/',
      'en' => $b . '/en/guides/air-conditioning-holiday-apartments-benidorm/',
      'de' => $b . '/de/ratgeber/klimaanlage-ferienwohnungen-benidorm/',
      'nl' => $b . '/nl/gidsen/airco-vakantieappartementen-benidorm/',
      'ru' => $b . '/ru/gidy/konditsioner-dlya-turisticheskih-apartamentov-benidorm/',
      'no' => $b . '/no/guider/aircondition-ferieleiligheter-benidorm/',
    ],
    'repair_or_replace' => [
      'es' => $b . '/es/blog/reparar-o-cambiar-aire-acondicionado/',
      'en' => $b . '/en/guides/repair-or-replace-air-conditioning/',
      'de' => $b . '/de/ratgeber/klimaanlage-reparieren-oder-ersetzen/',
      'nl' => $b . '/nl/gidsen/airco-repareren-of-vervangen/',
      'ru' => $b . '/ru/gidy/remont-ili-zamena-konditsionera/',
      'no' => $b . '/no/guider/reparere-eller-bytte-aircondition/',
    ],
    'save_electricity' => [
      'es' => $b . '/es/blog/como-ahorrar-luz-aire-acondicionado/',
      'en' => $b . '/en/guides/how-to-save-electricity-air-conditioning/',
      'de' => $b . '/de/ratgeber/strom-sparen-mit-klimaanlage/',
      'nl' => $b . '/nl/gidsen/stroom-besparen-met-airco/',
      'ru' => $b . '/ru/gidy/kak-ekonomit-elektroenergiyu-s-konditsionerom/',
      'no' => $b . '/no/guider/spare-strom-med-aircondition/',
    ],
    'heat_pump' => [
      'es' => $b . '/es/blog/aerotermia-bomba-calor-cuando-merece-la-pena/',
      'en' => $b . '/en/guides/heat-pump-aerothermal-when-worth-it/',
      'de' => $b . '/de/ratgeber/waermepumpe-aerothermie-wann-lohnt-es-sich/',
      'nl' => $b . '/nl/gidsen/warmtepomp-aerothermie-wanneer-de-moeite-waard/',
      'ru' => $b . '/ru/gidy/teplovoj-nasos-aerotermiya-kogda-vygodno/',
      'no' => $b . '/no/guider/varmepumpe-aerotermi-nar-lonner-det-seg/',
    ],
    'capacity' => [
      'es' => $b . '/es/blog/que-potencia-aire-acondicionado-necesita-vivienda/',
      'en' => $b . '/en/guides/what-air-conditioning-capacity-home-needs/',
      'de' => $b . '/de/ratgeber/welche-klimaanlagen-leistung-wohnung-benoetigt/',
      'nl' => $b . '/nl/gidsen/welk-vermogen-airco-woning-nodig/',
      'ru' => $b . '/ru/gidy/kakaya-moshchnost-konditsionera-nuzhna-dlya-doma/',
      'no' => $b . '/no/guider/hvilken-kapasitet-aircondition-trenger-bolig/',
    ],
    'maintenance' => [
      'es' => $b . '/es/blog/mantenimiento-aire-acondicionado-antes-verano/',
      'en' => $b . '/en/guides/air-conditioning-maintenance-before-summer/',
      'de' => $b . '/de/ratgeber/klimaanlagen-wartung-vor-dem-sommer/',
      'nl' => $b . '/nl/gidsen/airco-onderhoud-voor-de-zomer/',
      'ru' => $b . '/ru/gidy/obsluzhivanie-konditsionera-pered-letom/',
      'no' => $b . '/no/guider/vedlikehold-aircondition-for-sommeren/',
    ],
  ];
  return $map;
}

function guide_hreflang_html(string $guideKey): string {
  $map = guide_hreflang_map();
  $urls = $map[$guideKey] ?? [];
  if (empty($urls)) {
    return '';
  }
  $html = '';
  foreach ($urls as $lang => $url) {
    $html .= '<link rel="alternate" hreflang="' . e($lang) . '" href="' . e($url) . '">' . "\n";
  }
  $html .= '<link rel="alternate" hreflang="x-default" href="' . e($urls['es'] ?? reset($urls)) . '">' . "\n";
  return $html;
}

function es_guide_page_for_path(string $path): ?array {
  static $cache = null;
  if ($cache === null) {
    $file = __DIR__ . '/content/guides/es.php';
    $loaded = is_file($file) ? require $file : [];
    $cache = is_array($loaded) ? $loaded : [];
  }
  return $cache[$path] ?? null;
}

function lang_guide_page_for_path(string $path, string $lang): ?array {
  static $caches = [];
  if (!isset($caches[$lang])) {
    $file = __DIR__ . '/content/guides/' . $lang . '.php';
    $loaded = is_file($file) ? require $file : [];
    $caches[$lang] = is_array($loaded) ? $loaded : [];
  }
  return $caches[$lang][$path] ?? null;
}

function guide_ui_labels(string $lang): array {
  static $labels = [
    'es' => [
      'intro_kicker'    => 'Gu&iacute;a',
      'faq_kicker'      => 'Dudas habituales',
      'faq_h2'          => 'Preguntas frecuentes',
      'services_kicker' => 'Servicios',
      'services_h2'     => 'Servicios relacionados',
      'areas_kicker'    => 'Zonas',
      'areas_h2'        => 'Zonas donde trabajamos',
    ],
    'en' => [
      'intro_kicker'    => 'Guide',
      'faq_kicker'      => 'Common questions',
      'faq_h2'          => 'Frequently asked questions',
      'services_kicker' => 'Services',
      'services_h2'     => 'Related services',
      'areas_kicker'    => 'Areas',
      'areas_h2'        => 'Areas we serve',
    ],
    'de' => [
      'intro_kicker'    => 'Ratgeber',
      'faq_kicker'      => 'H&auml;ufige Fragen',
      'faq_h2'          => 'H&auml;ufig gestellte Fragen',
      'services_kicker' => 'Leistungen',
      'services_h2'     => 'Verwandte Leistungen',
      'areas_kicker'    => 'Gebiete',
      'areas_h2'        => 'Gebiete, in denen wir t&auml;tig sind',
    ],
    'nl' => [
      'intro_kicker'    => 'Gids',
      'faq_kicker'      => 'Veelgestelde vragen',
      'faq_h2'          => 'Veelgestelde vragen',
      'services_kicker' => 'Diensten',
      'services_h2'     => 'Gerelateerde diensten',
      'areas_kicker'    => 'Gebieden',
      'areas_h2'        => 'Gebieden waar wij actief zijn',
    ],
    'ru' => [
      'intro_kicker'    => '&#1056;&#1091;&#1082;&#1086;&#1074;&#1086;&#1076;&#1089;&#1090;&#1074;&#1086;',
      'faq_kicker'      => '&#1063;&#1072;&#1089;&#1090;&#1099;&#1077; &#1074;&#1086;&#1087;&#1088;&#1086;&#1089;&#1099;',
      'faq_h2'          => '&#1063;&#1072;&#1089;&#1090;&#1086; &#1079;&#1072;&#1076;&#1072;&#1074;&#1072;&#1077;&#1084;&#1099;&#1077; &#1074;&#1086;&#1087;&#1088;&#1086;&#1089;&#1099;',
      'services_kicker' => '&#1059;&#1089;&#1083;&#1091;&#1075;&#1080;',
      'services_h2'     => '&#1057;&#1074;&#1103;&#1079;&#1072;&#1085;&#1085;&#1099;&#1077; &#1091;&#1089;&#1083;&#1091;&#1075;&#1080;',
      'areas_kicker'    => '&#1056;&#1072;&#1081;&#1086;&#1085;&#1099;',
      'areas_h2'        => '&#1056;&#1072;&#1081;&#1086;&#1085;&#1099; &#1085;&#1072;&#1096;&#1077;&#1081; &#1088;&#1072;&#1073;&#1086;&#1090;&#1099;',
    ],
    'no' => [
      'intro_kicker'    => 'Guide',
      'faq_kicker'      => 'Vanlige sp&oslash;rsm&aring;l',
      'faq_h2'          => 'Vanlige sp&oslash;rsm&aring;l',
      'services_kicker' => 'Tjenester',
      'services_h2'     => 'Relaterte tjenester',
      'areas_kicker'    => 'Omr&aring;der',
      'areas_h2'        => 'Omr&aring;der vi betjener',
    ],
  ];
  return $labels[$lang] ?? $labels['en'];
}

function render_guide_detail_body(array $guide, string $lang): string {
  $guideUi = guide_ui_labels($lang);
  ob_start();
  include __DIR__ . '/../views/guide_detail.php';
  return ob_get_clean() ?: '';
}
