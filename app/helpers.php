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
        $cfg = config('brand.domain');
        if ($cfg) return rtrim($cfg, '/');
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host;
    }
}

if (!function_exists('lang_url')) {
    function lang_url(string $lang): string {
        $base = rtrim(base_url(), '/');
        $def  = config('brand.default_lang', 'es');
        if ($lang === $def) return $base . '/';
        return $base . '/' . $lang . '/';
    }
}

if (!function_exists('lang_switch_url')) {
    function lang_switch_url(string $lang): string {
        return lang_url($lang) . '?setlang=' . rawurlencode($lang);
    }
}

if (!function_exists('flag_url')) {
    function flag_url(string $lang): string {
        return asset('img/flags/' . $lang . '.svg');
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
            echo '<link rel="alternate" hreflang="'.e($l).'" href="'.e(lang_url($l)).'">' . "\n";
        }
        echo '<link rel="alternate" hreflang="x-default" href="'.e(lang_url($def)).'">' . "\n";
    }
}

if (!function_exists('print_og_locales')) {
    function print_og_locales(): void {
        $map = ['es'=>'es_ES','en'=>'en_GB','de'=>'de_DE','nl'=>'nl_NL','ru'=>'ru_RU'];
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
        $lang  = $GLOBALS['current_lang'] ?? config('brand.default_lang', 'es');
        $brand = config('brand.name', '+QUECLIMA');
        $logo  = base_url() . asset('img/masqueclimalogo_.png');
        $url   = lang_url($lang);
        $data  = [
            '@context' => 'https://schema.org',
            '@type'    => 'HVACBusiness',
            'name'     => $brand,
            'url'      => $url,
            'logo'     => $logo,
        ];
        echo '<script type="application/ld+json">'.json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."</script>\n";
    }
}

?>
