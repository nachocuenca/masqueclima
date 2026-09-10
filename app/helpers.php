<?php
// Helpers de rutas y SEO

if (!function_exists('asset')) {
    function asset(string $path): string {
        $path = ltrim($path, '/');
        $segments = array_map('rawurlencode', explode('/', $path));
        return '/assets/' . implode('/', $segments);
    }
}

if (!function_exists('base_url')) {
    function base_url(): string {
        return canonical_base_url();
    }
}

if (!function_exists('canonical_base_url')) {
    function canonical_base_url(): string {
        $cfg = getenv('CANONICAL_BASE_URL') ?: config('brand.domain');
        if ($cfg) return rtrim($cfg, '/');
        return 'https://masqueclima.es';
    }
}

if (!function_exists('runtime_base_url')) {
    function runtime_base_url(): string {
        $cfg = getenv('APP_BASE_URL') ?: config('app.base_url');
        if ($cfg) return rtrim($cfg, '/');
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host;
    }
}

if (!function_exists('internal_url')) {
    function internal_url(string $path): string {
        $path = '/' . ltrim($path, '/');
        return preg_replace('#/+#', '/', $path) ?: '/';
    }
}

if (!function_exists('lang_url')) {
    function lang_url(string $lang, bool $absolute = false): string {
        $path = '/' . rawurlencode($lang) . '/';
        return $absolute ? runtime_base_url() . $path : $path;
    }
}

if (!function_exists('seo_lang_url')) {
    function seo_lang_url(string $lang): string {
        return canonical_base_url() . '/' . rawurlencode($lang) . '/';
    }
}

if (!function_exists('lang_switch_url')) {
    function lang_switch_url(string $lang): string {
        $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        return localized_equivalent_url(is_string($currentPath) ? $currentPath : '/', $lang);
    }
}

if (!function_exists('flag_url')) {
    function flag_url(string $lang): string {
        return asset('img/flags/' . $lang . '.svg');
    }
}

if (!function_exists('public_asset_exists')) {
    function public_asset_exists(?string $path): bool {
        if (!is_string($path) || $path === '') {
            return false;
        }

        $cleanPath = parse_url($path, PHP_URL_PATH);
        if (!is_string($cleanPath) || !str_starts_with($cleanPath, '/assets/') || str_contains($cleanPath, '..')) {
            return false;
        }

        return is_file(__DIR__ . '/../public' . $cleanPath);
    }
}

if (!function_exists('render_partial')) {
    function render_partial(string $name, array $data = []): string {
        if (!preg_match('/\A[a-z0-9_-]+\z/', $name)) {
            return '';
        }

        $base = realpath(__DIR__ . '/../views/partials');
        $file = realpath(__DIR__ . '/../views/partials/' . $name . '.php');
        if ($base === false || $file === false || !str_starts_with($file, $base . DIRECTORY_SEPARATOR)) {
            return '';
        }

        ob_start();
        extract($data, EXTR_SKIP);
        include $file;
        return (string) ob_get_clean();
    }
}

if (!function_exists('accepting_new_work')) {
    function accepting_new_work(): bool {
        return (bool) config('app.accepting_new_work', false);
    }
}

if (!function_exists('closed_agenda_copy')) {
    function closed_agenda_copy(string $lang): array {
        $copy = [
            'es' => [
                'title' => 'AGENDA TEMPORALMENTE CERRADA',
                'body' => 'En estos momentos no estamos aceptando nuevos trabajos ni solicitudes de presupuesto.',
                'existing' => 'Si ya tienes un trabajo en curso con +QUECLIMA, puedes contactar con nosotros por los canales habituales.',
                'short' => 'Agenda cerrada temporalmente',
                'unavailable' => 'Solicitudes de presupuesto no disponibles',
                'call' => 'Llamar por trabajo en curso',
                'whatsapp' => 'WhatsApp para trabajos en curso',
            ],
            'en' => [
                'title' => 'SCHEDULE TEMPORARILY CLOSED',
                'body' => 'We are not accepting new jobs or quote requests at the moment.',
                'existing' => 'If you already have work in progress with +QUECLIMA, you can contact us through the usual channels.',
                'short' => 'Schedule temporarily closed',
                'unavailable' => 'Quote requests unavailable',
                'call' => 'Call about ongoing work',
                'whatsapp' => 'WhatsApp for ongoing work',
            ],
            'de' => [
                'title' => 'TERMINKALENDER VOR&Uuml;BERGEHEND GESCHLOSSEN',
                'body' => 'Derzeit nehmen wir keine neuen Auftr&auml;ge und keine Angebotsanfragen an.',
                'existing' => 'Wenn Sie bereits einen laufenden Auftrag mit +QUECLIMA haben, k&ouml;nnen Sie uns &uuml;ber die gewohnten Kan&auml;le kontaktieren.',
                'short' => 'Terminkalender vor&uuml;bergehend geschlossen',
                'unavailable' => 'Angebotsanfragen nicht verf&uuml;gbar',
                'call' => 'Anruf zu laufendem Auftrag',
                'whatsapp' => 'WhatsApp f&uuml;r laufende Auftr&auml;ge',
            ],
            'nl' => [
                'title' => 'AGENDA TIJDELIJK GESLOTEN',
                'body' => 'Op dit moment nemen we geen nieuwe opdrachten of offerteaanvragen aan.',
                'existing' => 'Heb je al lopend werk met +QUECLIMA, dan kun je contact opnemen via de gebruikelijke kanalen.',
                'short' => 'Agenda tijdelijk gesloten',
                'unavailable' => 'Offerteaanvragen niet beschikbaar',
                'call' => 'Bellen over lopend werk',
                'whatsapp' => 'WhatsApp voor lopend werk',
            ],
            'ru' => [
                'title' => '&#1047;&#1040;&#1055;&#1048;&#1057;&#1068; &#1042;&#1056;&#1045;&#1052;&#1045;&#1053;&#1053;&#1054; &#1047;&#1040;&#1050;&#1056;&#1067;&#1058;&#1040;',
                'body' => '&#1057;&#1077;&#1081;&#1095;&#1072;&#1089; &#1084;&#1099; &#1085;&#1077; &#1087;&#1088;&#1080;&#1085;&#1080;&#1084;&#1072;&#1077;&#1084; &#1085;&#1086;&#1074;&#1099;&#1077; &#1079;&#1072;&#1082;&#1072;&#1079;&#1099; &#1080; &#1079;&#1072;&#1087;&#1088;&#1086;&#1089;&#1099; &#1085;&#1072; &#1089;&#1084;&#1077;&#1090;&#1091;.',
                'existing' => '&#1045;&#1089;&#1083;&#1080; &#1091; &#1074;&#1072;&#1089; &#1091;&#1078;&#1077; &#1077;&#1089;&#1090;&#1100; &#1090;&#1077;&#1082;&#1091;&#1097;&#1080;&#1081; &#1087;&#1088;&#1086;&#1077;&#1082;&#1090; &#1089; +QUECLIMA, &#1089;&#1074;&#1103;&#1078;&#1080;&#1090;&#1077;&#1089;&#1100; &#1089; &#1085;&#1072;&#1084;&#1080; &#1095;&#1077;&#1088;&#1077;&#1079; &#1086;&#1073;&#1099;&#1095;&#1085;&#1099;&#1077; &#1082;&#1072;&#1085;&#1072;&#1083;&#1099;.',
                'short' => '&#1047;&#1072;&#1087;&#1080;&#1089;&#1100; &#1074;&#1088;&#1077;&#1084;&#1077;&#1085;&#1085;&#1086; &#1079;&#1072;&#1082;&#1088;&#1099;&#1090;&#1072;',
                'unavailable' => '&#1047;&#1072;&#1087;&#1088;&#1086;&#1089;&#1099; &#1085;&#1072; &#1089;&#1084;&#1077;&#1090;&#1091; &#1085;&#1077;&#1076;&#1086;&#1089;&#1090;&#1091;&#1087;&#1085;&#1099;',
                'call' => '&#1055;&#1086;&#1079;&#1074;&#1086;&#1085;&#1080;&#1090;&#1100; &#1087;&#1086; &#1090;&#1077;&#1082;&#1091;&#1097;&#1077;&#1084;&#1091; &#1087;&#1088;&#1086;&#1077;&#1082;&#1090;&#1091;',
                'whatsapp' => 'WhatsApp &#1076;&#1083;&#1103; &#1090;&#1077;&#1082;&#1091;&#1097;&#1080;&#1093; &#1087;&#1088;&#1086;&#1077;&#1082;&#1090;&#1086;&#1074;',
            ],
            'no' => [
                'title' => 'KALENDEREN ER MIDLERTIDIG STENGT',
                'body' => 'For &oslash;yeblikket tar vi ikke imot nye jobber eller tilbudsforesp&oslash;rsler.',
                'existing' => 'Hvis du allerede har et p&aring;g&aring;ende arbeid med +QUECLIMA, kan du kontakte oss via de vanlige kanalene.',
                'short' => 'Kalenderen er midlertidig stengt',
                'unavailable' => 'Tilbudsforesp&oslash;rsler er ikke tilgjengelige',
                'call' => 'Ring om p&aring;g&aring;ende arbeid',
                'whatsapp' => 'WhatsApp for p&aring;g&aring;ende arbeid',
            ],
        ];

        return $copy[$lang] ?? $copy['es'];
    }
}

if (!function_exists('localized_hub_url')) {
    function localized_hub_url(string $lang, string $hub): ?string {
        $paths = [
            'es' => [
                'services' => '/es/servicios/',
                'zones'    => '/es/zonas/',
                'guides'   => '/es/blog/',
            ],
            'en' => [
                'services' => '/en/services/',
                'zones'    => '/en/areas/',
                'guides'   => '/en/guides/',
            ],
            'de' => [
                'services' => '/de/dienstleistungen/',
                'zones'    => '/de/gebiete/',
                'guides'   => '/de/ratgeber/',
            ],
            'nl' => [
                'services' => '/nl/diensten/',
                'zones'    => '/nl/gebieden/',
                'guides'   => '/nl/gidsen/',
            ],
            'ru' => [
                'services' => '/ru/uslugi/',
                'zones'    => '/ru/raiony/',
                'guides'   => '/ru/gidy/',
            ],
            'no' => [
                'services' => '/no/tjenester/',
                'zones'    => '/no/omrader/',
                'guides'   => '/no/guider/',
            ],
        ];

        return $paths[$lang][$hub] ?? null;
    }
}

if (!function_exists('localized_service_equivalent_paths')) {
    function localized_service_equivalent_paths(): array {
        return [
            'installation' => [
                'es' => '/es/servicios/instalacion-aire-acondicionado/',
                'en' => '/en/services/air-conditioning-installation/',
                'de' => '/de/dienstleistungen/klimaanlage-installation/',
                'nl' => '/nl/diensten/airco-installatie/',
                'ru' => '/ru/uslugi/ustanovka-konditsionera/',
                'no' => '/no/tjenester/installasjon-av-aircondition/',
            ],
            'maintenance' => [
                'es' => '/es/servicios/mantenimiento-climatizacion/',
                'en' => '/en/services/climate-control-maintenance/',
                'de' => '/de/dienstleistungen/klimaanlagen-wartung/',
                'nl' => '/nl/diensten/klimaatbeheersing-onderhoud/',
                'ru' => '/ru/uslugi/obsluzhivanie-konditsionera/',
                'no' => '/no/tjenester/vedlikehold-av-klimaanlegg/',
            ],
            'repair' => [
                'es' => '/es/servicios/reparacion-aire-acondicionado/',
                'en' => '/en/services/air-conditioning-repair/',
                'de' => '/de/dienstleistungen/klimaanlage-reparatur/',
                'nl' => '/nl/diensten/airco-reparatie/',
                'ru' => '/ru/uslugi/remont-konditsionera/',
                'no' => '/no/tjenester/reparasjon-av-aircondition/',
            ],
            'heat_pump' => [
                'es' => '/es/servicios/aerotermia-bomba-calor/',
                'en' => '/en/services/heat-pump-aerothermal/',
                'de' => '/de/dienstleistungen/waermepumpe-aerothermie/',
                'nl' => '/nl/diensten/warmtepomp-aerothermie/',
                'ru' => '/ru/uslugi/teplovoj-nasos/',
                'no' => '/no/tjenester/varmepumpe-aerotermi/',
            ],
            'solar_thermal' => [
                'es' => '/es/servicios/energia-solar-termica/',
                'en' => '/en/services/solar-thermal-energy/',
                'de' => '/de/dienstleistungen/solarthermie/',
                'nl' => '/nl/diensten/zonneboiler-zonneenergie/',
                'ru' => '/ru/uslugi/solnechnye-kollektory/',
                'no' => '/no/tjenester/solvarme/',
            ],
        ];
    }
}

if (!function_exists('localized_normalize_path')) {
    function localized_normalize_path(string $path): string {
        $path = parse_url($path, PHP_URL_PATH) ?: '/';
        $path = '/' . ltrim($path, '/');
        if ($path !== '/' && !str_contains(basename($path), '.') && !str_ends_with($path, '/')) {
            $path .= '/';
        }
        return $path;
    }
}

if (!function_exists('localized_locality_prefixes')) {
    function localized_locality_prefixes(): array {
        return [
            'es' => '/es/aire-acondicionado-',
            'en' => '/en/air-conditioning-',
            'de' => '/de/klimaanlage-',
            'nl' => '/nl/airco-',
            'ru' => '/ru/konditsioner-',
            'no' => '/no/aircondition-',
        ];
    }
}

if (!function_exists('localized_snapshot_name_for_path')) {
    function localized_snapshot_name_for_path(string $path): string {
        $trimmed = trim($path, '/');
        if ($trimmed === '') {
            return 'root.html';
        }
        return preg_replace('/[^A-Za-z0-9._-]+/', '__', $trimmed) . '.html';
    }
}

if (!function_exists('localized_route_exists')) {
    function localized_route_exists(string $path): bool {
        $path = localized_normalize_path($path);
        $langs = config('brand.langs', ['es']);
        foreach ($langs as $lang) {
            if ($path === lang_url($lang)) {
                return true;
            }
            foreach (['services', 'zones', 'guides'] as $hub) {
                if ($path === localized_hub_url($lang, $hub)) {
                    return true;
                }
            }
        }
        foreach (localized_service_equivalent_paths() as $paths) {
            if (in_array($path, $paths, true)) {
                return true;
            }
        }

        return is_file(__DIR__ . '/snapshots/' . localized_snapshot_name_for_path($path));
    }
}

if (!function_exists('localized_equivalent_url')) {
    function localized_equivalent_url(string $currentPath, string $targetLang): string {
        $langs = config('brand.langs', ['es']);
        if (!in_array($targetLang, $langs, true)) {
            $targetLang = config('brand.default_lang', 'es');
        }

        $fallbackHome = lang_url($targetLang);
        $path = localized_normalize_path($currentPath);

        foreach ($langs as $lang) {
            if ($path === lang_url($lang)) {
                return $fallbackHome;
            }
        }

        foreach (['services', 'zones', 'guides'] as $hub) {
            foreach ($langs as $lang) {
                if ($path === localized_hub_url($lang, $hub)) {
                    return localized_hub_url($targetLang, $hub) ?? $fallbackHome;
                }
            }
        }

        foreach (localized_service_equivalent_paths() as $paths) {
            if (in_array($path, $paths, true)) {
                return $paths[$targetLang] ?? $fallbackHome;
            }
        }

        foreach (localized_locality_prefixes() as $prefix) {
            $pattern = '~^' . preg_quote($prefix, '~') . '([a-z0-9-]+)/$~';
            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }

            $targetPrefix = localized_locality_prefixes()[$targetLang] ?? null;
            if ($targetPrefix === null) {
                return $fallbackHome;
            }

            $candidate = $targetPrefix . $matches[1] . '/';
            if (localized_route_exists($candidate)) {
                return $candidate;
            }

            return localized_hub_url($targetLang, 'zones') ?? $fallbackHome;
        }

        return $fallbackHome;
    }
}

if (!function_exists('localized_hreflang_links_html')) {
    function localized_hreflang_links_html(string $currentPath): string {
        $langs = config('brand.langs', ['es']);
        $defaultLang = config('brand.default_lang', 'es');
        $base = canonical_base_url();
        $html = '';

        foreach ($langs as $lang) {
            $path = localized_equivalent_url($currentPath, $lang);
            $html .= '<link rel="alternate" hreflang="' . e($lang) . '" href="' . e($base . $path) . '">' . "\n";
        }

        $defaultPath = localized_equivalent_url($currentPath, $defaultLang);
        $html .= '<link rel="alternate" hreflang="x-default" href="' . e($base . $defaultPath) . '">' . "\n";

        return $html;
    }
}

if (!function_exists('primary_nav_items')) {
    function primary_nav_items(string $lang): array {
        if ($lang === 'es') {
            return [
                ['label' => 'Inicio', 'href' => '/es/'],
                ['label' => 'Método', 'href' => '/es/#metodo'],
                ['label' => 'Nosotros', 'href' => '/es/#nosotros'],
                ['label' => 'Zona', 'href' => '/es/#zona'],
                ['label' => 'FAQ', 'href' => '/es/#faq'],
                ['label' => 'Contacto', 'href' => '/es/#contacto'],
            ];
        }

        $items = [
            ['label' => t('nav.home', 'Inicio'), 'href' => lang_url($lang)],
            ['label' => t('nav.method', 'Metodo'), 'href' => lang_url($lang) . '#metodo'],
        ];

        if ($services = localized_hub_url($lang, 'services')) {
            $items[] = ['label' => t('nav.services', 'Servicios'), 'href' => $services];
        }

        if ($zones = localized_hub_url($lang, 'zones')) {
            $items[] = ['label' => t('nav.zones', 'Zonas'), 'href' => $zones];
        }

        if ($guides = localized_hub_url($lang, 'guides')) {
            $items[] = ['label' => t('nav.guides', 'Guias'), 'href' => $guides];
        }

        $items[] = ['label' => t('nav.faq', 'FAQ'), 'href' => lang_url($lang) . '#faq'];
        $items[] = ['label' => t('nav.contact', 'Contacto'), 'href' => lang_url($lang) . '#contacto'];

        return $items;
    }
}

if (!function_exists('meta_title')) {
    function meta_title(): string {
        $lang = $GLOBALS['current_lang'] ?? config('brand.default_lang', 'es');
        $titles = config('seo.title', []);
        return $titles[$lang] ?? ($titles[config('brand.default_lang', 'es')] ?? '');
    }
}

if (!function_exists('meta_description')) {
    function meta_description(): string {
        $lang = $GLOBALS['current_lang'] ?? config('brand.default_lang', 'es');
        $descs = config('seo.description', []);
        return $descs[$lang] ?? ($descs[config('brand.default_lang', 'es')] ?? '');
    }
}

if (!function_exists('print_hreflang')) {
    function print_hreflang(): void {
        $langs = config('brand.langs', ['es']);
        $def   = config('brand.default_lang', 'es');
        foreach ($langs as $l) {
            echo '<link rel="alternate" hreflang="'.e($l).'" href="'.e(seo_lang_url($l)).'">' . "\n";
        }
        echo '<link rel="alternate" hreflang="x-default" href="'.e(seo_lang_url($def)).'">' . "\n";
    }
}

if (!function_exists('print_og_locales')) {
    function print_og_locales(): void {
        $map = ['es'=>'es_ES','en'=>'en_GB','de'=>'de_DE','nl'=>'nl_NL','ru'=>'ru_RU','no'=>'nb_NO'];
        $langs = config('brand.langs', ['es']);
        $def   = config('brand.default_lang', 'es');
        $defLoc = $map[$def] ?? 'es_ES';
        echo '<meta property="og:locale" content="'.e($defLoc).'">' . "\n";
        foreach ($langs as $l) {
            if ($l === $def) continue;
            if (isset($map[$l])) {
                echo '<meta property="og:locale:alternate" content="'.e($map[$l]).'">' . "\n";
            }
        }
    }
}

if (!function_exists('print_jsonld')) {
    function print_jsonld(): void {
        $lang   = $GLOBALS['current_lang'] ?? config('brand.default_lang', 'es');
        $brand  = config('brand.name', '+QUECLIMA');
        $logo   = base_url() . asset('img/masqueclimalogo_.png');
        $url    = seo_lang_url($lang);
        $phone  = config('brand.phone');
        $langs  = config('brand.langs', ['es']);

        // Organization
        $org = [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => $brand,
            'url'      => seo_lang_url(config('brand.default_lang', 'es')),
            'logo'     => $logo,
        ];
        if ($same = config('brand.sameAs', [])) $org['sameAs'] = $same;
        echo '<script type="application/ld+json">' . json_encode($org, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</script>\n";

        // HVACBusiness
        $hvac = [
            '@context' => 'https://schema.org',
            '@type'    => 'HVACBusiness',
            'name'     => $brand,
            'url'      => $url,
            'logo'     => $logo,
            'areaServed'   => config('brand.area'),
            'serviceType'  => ['Instalación aire acondicionado','Mantenimiento climatización','Energía solar'],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => $phone,
                'contactType' => 'customer service',
                'availableLanguage' => $langs,
            ],
        ];
        echo '<script type="application/ld+json">' . json_encode($hvac, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</script>\n";

        // WebSite
        $site = [
            '@context' => 'https://schema.org',
            '@type'    => 'WebSite',
            'url'      => seo_lang_url(config('brand.default_lang', 'es')),
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => base_url() . '/search?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
        echo '<script type="application/ld+json">' . json_encode($site, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</script>\n";

        // Breadcrumb (mínimo Home)
        $crumb = [
            '@context' => 'https://schema.org',
            '@type'    => 'BreadcrumbList',
            'itemListElement' => [[
                '@type'    => 'ListItem',
                'position' => 1,
                'name'     => t('nav.home'),
                'item'     => $url,
            ]],
        ];
        echo '<script type="application/ld+json">' . json_encode($crumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</script>\n";

        // FAQPage (solo en home)
        if (($GLOBALS['__view_name__'] ?? '') === 'home') {
            $faqs = [];
            for ($i = 1; $i <= 8; $i++) {
                $q = t('faq.q' . $i);
                $a = t('faq.a' . $i);
                if ($q === 'faq.q' . $i || $a === 'faq.a' . $i) continue;
                if ($i === 3) {
                    $items = [];
                    for ($j = 1; $j <= 5; $j++) {
                        $li = t('faq.a3.i' . $j);
                        if ($li !== 'faq.a3.i' . $j) $items[] = $li;
                    }
                    if ($items) $a .= ' ' . implode(' ', $items);
                }
                $faqs[] = [
                    '@type' => 'Question',
                    'name'  => $q,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => $a,
                    ],
                ];
            }
            if ($faqs) {
                $faqPage = [
                    '@context'    => 'https://schema.org',
                    '@type'       => 'FAQPage',
                    'mainEntity'  => $faqs,
                ];
                echo '<script type="application/ld+json">' . json_encode($faqPage, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</script>\n";
            }
        }
    }
}

?>
