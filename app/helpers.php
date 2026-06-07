<?php
// Helpers de rutas y SEO

if (!function_exists('asset')) {
    function asset(string $path): string {
        $path = ltrim($path, '/');
        $segments = array_map('rawurlencode', explode('/', $path));
        return '/assets/' . implode('/', $segments);
    }
}

if (!function_exists('versioned_asset')) {
    function versioned_asset(string $path): string {
        $url = str_starts_with($path, '/assets/') ? $path : asset($path);
        $cleanPath = parse_url($url, PHP_URL_PATH);

        if (!is_string($cleanPath) || !str_starts_with($cleanPath, '/assets/') || str_contains($cleanPath, '..')) {
            return $url;
        }

        $file = __DIR__ . '/../public' . $cleanPath;
        if (!is_file($file)) {
            return $url;
        }

        return $url . (str_contains($url, '?') ? '&' : '?') . 'v=' . rawurlencode((string) filemtime($file));
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

if (!function_exists('localized_guide_equivalent_paths')) {
    function localized_guide_equivalent_paths(): array {
        return [
            'capacity' => [
                'es' => '/es/blog/que-potencia-aire-acondicionado-necesita-vivienda/',
                'en' => '/en/guides/what-air-conditioning-capacity-home-needs/',
                'de' => '/de/ratgeber/welche-klimaanlagen-leistung-wohnung-benoetigt/',
                'nl' => '/nl/gidsen/welk-vermogen-airco-woning-nodig/',
                'ru' => '/ru/gidy/kakaya-moshchnost-konditsionera-nuzhna-dlya-doma/',
                'no' => '/no/guider/hvilken-kapasitet-aircondition-trenger-bolig/',
            ],
            'maintenance' => [
                'es' => '/es/blog/mantenimiento-aire-acondicionado-antes-verano/',
                'en' => '/en/guides/air-conditioning-maintenance-before-summer/',
                'de' => '/de/ratgeber/klimaanlagen-wartung-vor-dem-sommer/',
                'nl' => '/nl/gidsen/airco-onderhoud-voor-de-zomer/',
                'ru' => '/ru/gidy/obsluzhivanie-konditsionera-pered-letom/',
                'no' => '/no/guider/vedlikehold-aircondition-for-sommeren/',
            ],
            'heat_pump' => [
                'es' => '/es/blog/aerotermia-bomba-calor-cuando-merece-la-pena/',
                'en' => '/en/guides/heat-pump-aerothermal-when-worth-it/',
                'de' => '/de/ratgeber/waermepumpe-aerothermie-wann-lohnt-es-sich/',
                'nl' => '/nl/gidsen/warmtepomp-aerothermie-wanneer-de-moeite-waard/',
                'ru' => '/ru/gidy/teplovoj-nasos-aerotermiya-kogda-vygodno/',
                'no' => '/no/guider/varmepumpe-aerotermi-nar-lonner-det-seg/',
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

if (!function_exists('nearby_locality_slugs')) {
    function nearby_locality_slugs(string $slug): array {
        $map = [
            'benidorm'           => ['altea', 'finestrat', 'la-nucia', 'villajoyosa', 'albir'],
            'altea'              => ['benidorm', 'calpe', 'albir', 'alfaz-del-pi', 'la-nucia'],
            'calpe'              => ['altea', 'benidorm', 'finestrat', 'la-nucia', 'albir'],
            'finestrat'          => ['benidorm', 'la-nucia', 'villajoyosa', 'altea', 'polop'],
            'la-nucia'           => ['benidorm', 'altea', 'polop', 'alfaz-del-pi', 'finestrat'],
            'guadalest'          => ['polop', 'benimantell', 'benifato', 'beniarda', 'confrides'],
            'villajoyosa'        => ['benidorm', 'finestrat', 'orxeta', 'relleu', 'altea'],
            'albir'              => ['altea', 'alfaz-del-pi', 'la-nucia', 'benidorm', 'calpe'],
            'alfaz-del-pi'       => ['albir', 'la-nucia', 'altea', 'benidorm', 'polop'],
            'beniarda'           => ['guadalest', 'polop', 'callosa-den-sarria', 'benimantell', 'confrides'],
            'benifato'           => ['guadalest', 'callosa-den-sarria', 'relleu', 'benimantell', 'polop'],
            'benimantell'        => ['guadalest', 'benifato', 'callosa-den-sarria', 'beniarda', 'polop'],
            'bolulla'            => ['callosa-den-sarria', 'tarbena', 'polop', 'guadalest', 'la-nucia'],
            'callosa-den-sarria' => ['polop', 'la-nucia', 'guadalest', 'benimantell', 'bolulla'],
            'confrides'          => ['guadalest', 'beniarda', 'callosa-den-sarria', 'polop', 'benimantell'],
            'orxeta'             => ['relleu', 'villajoyosa', 'finestrat', 'sella', 'benidorm'],
            'polop'              => ['la-nucia', 'callosa-den-sarria', 'finestrat', 'alfaz-del-pi', 'guadalest'],
            'relleu'             => ['orxeta', 'sella', 'villajoyosa', 'finestrat', 'benidorm'],
            'sella'              => ['relleu', 'orxeta', 'guadalest', 'villajoyosa', 'benidorm'],
            'tarbena'            => ['bolulla', 'callosa-den-sarria', 'guadalest', 'benimantell', 'polop'],
        ];
        $default = ['benidorm', 'altea', 'calpe', 'finestrat', 'la-nucia'];
        $neighbors = $map[$slug] ?? $default;
        return array_values(array_filter($neighbors, fn($s) => $s !== $slug));
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

        if (function_exists('localized_guide_equivalent_paths')) {
            foreach (localized_guide_equivalent_paths() as $paths) {
                if (in_array($path, $paths, true)) {
                    return $paths[$targetLang] ?? (localized_hub_url($targetLang, 'guides') ?? $fallbackHome);
                }
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
        return [
            ['label' => t('nav.home', 'Inicio'), 'href' => lang_url($lang)],
            ['label' => t('nav.method', 'Método'), 'href' => lang_url($lang) . '#metodo'],
            ['label' => t('nav.about', 'Nosotros'), 'href' => lang_url($lang) . '#nosotros'],
            ['label' => t('nav.coverage', 'Zona'), 'href' => lang_url($lang) . '#zona'],
            ['label' => t('nav.faq', 'FAQ'), 'href' => lang_url($lang) . '#faq'],
            ['label' => t('nav.contact', 'Contacto'), 'href' => lang_url($lang) . '#contacto'],
        ];
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
