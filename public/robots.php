<?php
header('Content-Type: text/plain; charset=utf-8');
$host = parse_url(base_url(), PHP_URL_HOST);
echo "User-agent: *\n";
echo "Disallow: /storage/\n";
echo "Allow: /\n";
echo "Sitemap: ".base_url()."/sitemap.xml\n";
