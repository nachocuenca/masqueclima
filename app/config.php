<?php
return [
  'brand' => [
    'name' => '+QUECLIMA',
    'alt_name' => 'Masqueclima',
    'domain' => 'https://masqueclima.es',
    'phone' => '+34 613 02 66 00',
    'email' => 'info@masqueclima.es',
    'area' => 'Provincia de Alicante, España',
    'default_lang' => 'es',
    'langs' => ['es','en','de','nl','ru'],
  ],
  'seo' => [
    'title' => [
      'es' => 'Instalación y Mantenimiento de Climatización | +QUECLIMA',
      'en' => 'Air Conditioning Installation & Maintenance | +QUECLIMA',
      'de' => 'Klimaanlagen: Installation & Wartung | +QUECLIMA',
      'nl' => 'Airconditioning: Installatie & Onderhoud | +QUECLIMA',
      'ru' => 'Климатические системы: монтаж и обслуживание | +QUECLIMA',
    ],
    'description' => [
      'es' => 'Expertos en aire acondicionado, calefacción y energía solar en Alicante. Instalación, mantenimiento y reparación con 3 años de garantía.',
      'en' => 'Experts in air conditioning, heating and solar thermal in Alicante. Installation, maintenance and repair with 3‑year warranty.',
      'de' => 'Experten für Klimaanlagen, Heizung und Solarthermie in Alicante. Installation, Wartung und Reparatur mit 3 Jahren Garantie.',
      'nl' => 'Experts in airco, verwarming en zonne-energie in Alicante. Installatie, onderhoud en reparatie met 3 jaar garantie.',
      'ru' => 'Эксперты по кондиционированию, отоплению и солнечным системам в Аликанте. Монтаж, обслуживание и ремонт с 3‑летней гарантией.',
    ]
  ],
  'smtp' => [
    'host' => getenv('SMTP_HOST') ?: null,
    'port' => getenv('SMTP_PORT') ?: 587,
    'user' => getenv('SMTP_USER') ?: null,
    'pass' => getenv('SMTP_PASS') ?: null,
    'secure' => getenv('SMTP_SECURE') ?: 'tls',
    'from' => getenv('SMTP_FROM') ?: 'no-reply@masqueclima.es',
    'from_name' => getenv('SMTP_FROM_NAME') ?: '+QUECLIMA'
  ],
  'app' => [
    'env' => getenv('APP_ENV') ?: 'production',
    'ga4_id' => getenv('GA4_ID') ?: null
  ]
];
