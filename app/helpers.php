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
        return lang_url($lang);
    }
}

if (!function_exists('flag_url')) {
    function flag_url(string $lang): string {
        return asset('img/flags/' . $lang . '.svg');
    }
}

if (!function_exists('localized_hub_url')) {
    function localized_hub_url(string $lang, string $hub): ?string {
        $paths = [
            'es' => [
                'services' => '/es/servicios/',
                'zones' => '/es/zonas/',
            ],
        ];

        return $paths[$lang][$hub] ?? null;
    }
}

if (!function_exists('primary_nav_items')) {
    function primary_nav_items(string $lang): array {
        $items = [
            ['label' => t('nav.home', 'Inicio'), 'href' => lang_url($lang)],
        ];

        if ($services = localized_hub_url($lang, 'services')) {
            $items[] = ['label' => t('nav.services', 'Servicios'), 'href' => $services];
        }

        if ($zones = localized_hub_url($lang, 'zones')) {
            $items[] = ['label' => t('nav.zones', 'Zonas'), 'href' => $zones];
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
