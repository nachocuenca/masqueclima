<?php
// Utilidades de URLs/Assets
if (!function_exists('asset')) {
  function asset(string $path): string { return '/'.ltrim($path,'/'); }
}
if (!function_exists('base_url')) {
  function base_url(): string {
    $cfg = $GLOBALS['config']['app']['base_url'] ?? '';
    if (!empty($cfg)) return rtrim($cfg, '/');
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme.'://'.$host;
  }
}
if (!function_exists('current_path_no_lang')) {
  // devuelve el path sin el prefijo de idioma
  function current_path_no_lang(): string {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $segs = array_values(array_filter(explode('/', trim($uri,'/'))));
    $langs = $GLOBALS['config']['brand']['langs'] ?? ['es'];
    if (!empty($segs) && in_array($segs[0], $langs, true)) array_shift($segs);
    return '/'.implode('/', $segs);
  }
}

// Idiomas / hreflang / lang URLs
if (!function_exists('lang_url')) {
  function lang_url(string $lang, string $path=''): string {
    $def = $GLOBALS['config']['brand']['default_lang'] ?? 'es';
    $path = trim($path ?: current_path_no_lang(), '/');
    if ($lang === $def) return rtrim(base_url().'/'.$path, '/');
    return rtrim(base_url().'/'.$lang.'/'.$path, '/');
  }
}
if (!function_exists('lang_switch_url')) {
  function lang_switch_url(string $lang, string $path=''): string {
    $u = lang_url($lang, $path);
    return $u.(strpos($u,'?')!==false ? '&' : '?').'setlang='.$lang;
  }
}
if (!function_exists('print_hreflang')) {
  function print_hreflang(): void {
    $langs = $GLOBALS['config']['brand']['langs'] ?? ['es'];
    foreach ($langs as $l) {
      echo '<link rel="alternate" hreflang="'.e($l).'" href="'.e(lang_url($l)).'">'."\n";
    }
    $def = $GLOBALS['config']['brand']['default_lang'] ?? 'es';
    echo '<link rel="alternate" hreflang="x-default" href="'.e(lang_url($def)).'">'."\n";
  }
}

// OG locales
if (!function_exists('print_og_locales')) {
  function print_og_locales(): void {
    $map = ['es'=>'es_ES','en'=>'en_GB','de'=>'de_DE','nl'=>'nl_NL','ru'=>'ru_RU'];
    $def = $GLOBALS['config']['brand']['default_lang'] ?? 'es';
    $langs = $GLOBALS['config']['brand']['langs'] ?? ['es'];
    $defLocale = $map[$def] ?? 'es_ES';
    echo '<meta property="og:locale" content="'.e($defLocale).'">'."\n";
    foreach ($langs as $l) {
      if ($l === $def) continue;
      $loc = $map[$l] ?? null;
      if ($loc) echo '<meta property="og:locale:alternate" content="'.e($loc).'">'."\n";
    }
  }
}

// META dinámico: usa la vista actual (home, 404, …)
if (!function_exists('meta_title')) {
  function meta_title(): string {
    $view = $GLOBALS['__view_name__'] ?? ($GLOBALS['view'] ?? 'home');
    $key = 'meta.'.$view.'.title';
    $v = t($key);
    if ($v !== $key) return (string)$v;
    return (string)t('meta.title', '+QUECLIMA · Climatización en Alicante');
  }
}
if (!function_exists('meta_description')) {
  function meta_description(): string {
    $view = $GLOBALS['__view_name__'] ?? ($GLOBALS['view'] ?? 'home');
    $key = 'meta.'.$view.'.description';
    $v = t($key);
    if ($v !== $key) return (string)$v;
    return (string)t('meta.description', 'Instalación y mantenimiento de aire acondicionado, calefacción y energía en Alicante.');
  }
}

// Flags (para el selector)
if (!function_exists('flag_url')) {
  function flag_url(string $code): string {
    $map = ['es'=>'es','en'=>'gb','de'=>'de','nl'=>'nl','ru'=>'ru'];
    $cc = $map[strtolower($code)] ?? 'gb';
    return 'https://flagcdn.com/16x12/'.$cc.'.png';
  }
}

// JSON-LD
if (!function_exists('print_jsonld')) {
  function print_jsonld(): void {
    $lang   = $GLOBALS['current_lang'] ?? 'es';
    $def    = $GLOBALS['config']['brand']['default_lang'] ?? 'es';
    $brand  = '+QUECLIMA';
    $logo   = base_url().'/img/masqueclimalogo_.png';
    $url    = lang_url($lang);
    $tel    = '+34 613 02 66 00';

    // Rating (solo si config lo define; evita inconsistencias)
    $rating = $GLOBALS['config']['brand']['rating'] ?? null; // ['value'=>5.0,'count'=>51]

    // 1) LocalBusiness / HVACBusiness
    $hvac = [
      '@context' => 'https://schema.org',
      '@type'    => 'HVACBusiness',
      'name'     => $brand,
      'url'      => $url,
      'logo'     => $logo,
      'image'    => $logo,
      'description' => (string) t('meta.home.description', t('meta.description','Servicio de climatización en Alicante')),
      'areaServed'  => [
        '@type' => 'AdministrativeArea',
        'name'  => (string) t('coverage.area_name','Provincia de Alicante')
      ],
      'contactPoint' => [
        '@type'           => 'ContactPoint',
        'telephone'       => $tel,
        'contactType'     => 'customer service',
        'availableLanguage' => [$lang==='es'?'Spanish':($lang==='en'?'English':strtoupper($lang))]
      ],
      'makesOffer' => [
        '@type' => 'OfferCatalog',
        'name'  => (string) t('offers.title','Servicios'),
        'itemListElement' => [
          ['@type'=>'Offer','itemOffered'=>['@type'=>'Service','name'=>(string)t('service.install_ac')]],
          ['@type'=>'Offer','itemOffered'=>['@type'=>'Service','name'=>(string)t('service.maintenance')]],
          ['@type'=>'Offer','itemOffered'=>['@type'=>'Service','name'=>(string)t('service.install_heating')]],
          ['@type'=>'Offer','itemOffered'=>['@type'=>'Service','name'=>(string)t('service.electrical')]],
          ['@type'=>'Offer','itemOffered'=>['@type'=>'Service','name'=>(string)t('service.plumbing')]],
        ]
      ],
      'sameAs' => [
        'https://g.co/kgs/QL4xiZy'
      ],
    ];
    if (is_array($rating) && isset($rating['value'],$rating['count'])) {
      $hvac['aggregateRating'] = [
        '@type' => 'AggregateRating',
        'ratingValue' => (string)$rating['value'],
        'reviewCount' => (string)$rating['count'],
      ];
    }

    // 2) FAQPage (lee traducciones)
    $faqItems = [];
    for ($i=1; $i<=8; $i++) {
      $q = t("faq.q{$i}");
      $a = t("faq.a{$i}");
      if ($q !== "faq.q{$i}" && $a !== "faq.a{$i}") {
        $faqItems[] = [
          '@type' => 'Question',
          'name'  => $q,
          'acceptedAnswer' => ['@type'=>'Answer','text'=>$a]
        ];
      }
    }
    $faq = null;
    if (!empty($faqItems)) {
      $faq = [
        '@context' => 'https://schema.org',
        '@type'    => 'FAQPage',
        'mainEntity' => $faqItems
      ];
    }

    // 3) VideoObject (hero)
    $video = [
      '@context' => 'https://schema.org',
      '@type'    => 'VideoObject',
      'name'     => (string) t('hero.title', $brand),
      'description' => (string) t('hero.subtitle', ''),
      'thumbnailUrl' => [$logo],
      'uploadDate'   => date('c', strtotime('-180 days')),
      'contentUrl'   => base_url().'/img/0_Air_Conditioner_Remote_Control_3840x2160.mp4',
      'embedUrl'     => $url.'#inicio'
    ];

    // 4) BreadcrumbList (Home)
    $breadcrumb = [
      '@context' => 'https://schema.org',
      '@type'    => 'BreadcrumbList',
      'itemListElement' => [
        ['@type'=>'ListItem','position'=>1,'name'=> (string) t('nav.home','Inicio'),'item'=>$url],
      ],
    ];

    // Imprimir
    echo '<script type="application/ld+json">'.json_encode($hvac, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."</script>\n";
    if ($faq)  echo '<script type="application/ld+json">'.json_encode($faq, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."</script>\n";
    echo '<script type="application/ld+json">'.json_encode($video, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."</script>\n";
    echo '<script type="application/ld+json">'.json_encode($breadcrumb, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."</script>\n";
  }
}
