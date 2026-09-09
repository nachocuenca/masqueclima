<?php
declare(strict_types=1);

/**
 * Estado comercial temporal del sitio.
 *
 * Se aplica al HTML final (snapshots y vistas dinámicas) para poder cerrar la
 * captación sin tocar URLs, metadatos SEO, canonical, hreflang ni contenido.
 */

if (!function_exists('site_status_copy')) {
  function site_status_copy(string $lang): array {
    $copy = [
      'es' => [
        'title' => 'AGENDA TEMPORALMENTE CERRADA',
        'text' => 'En estos momentos no estamos aceptando nuevos trabajos, reservas ni solicitudes de presupuesto. Si ya tienes un trabajo en curso con +QUECLIMA, puedes contactar con nosotros por los canales habituales.',
        'call' => 'Trabajo en curso: llamar',
        'whatsapp' => 'Trabajo en curso: WhatsApp',
      ],
      'en' => [
        'title' => 'TEMPORARILY CLOSED TO NEW BOOKINGS',
        'text' => 'We are currently not accepting new jobs, bookings or quote requests. If you already have work in progress with +QUECLIMA, you can contact us through the usual channels.',
        'call' => 'Existing job: call',
        'whatsapp' => 'Existing job: WhatsApp',
      ],
      'de' => [
        'title' => 'VORÜBERGEHEND KEINE NEUEN AUFTRÄGE',
        'text' => 'Derzeit nehmen wir keine neuen Aufträge, Termine oder Angebotsanfragen an. Wenn bereits ein Auftrag mit +QUECLIMA läuft, erreichen Sie uns weiterhin über die üblichen Kontaktwege.',
        'call' => 'Laufender Auftrag: anrufen',
        'whatsapp' => 'Laufender Auftrag: WhatsApp',
      ],
      'nl' => [
        'title' => 'TIJDELIJK GEEN NIEUWE AANVRAGEN',
        'text' => 'We nemen momenteel geen nieuwe opdrachten, afspraken of offerteaanvragen aan. Heb je al een lopende opdracht bij +QUECLIMA, dan kun je ons via de gebruikelijke kanalen bereiken.',
        'call' => 'Lopende opdracht: bellen',
        'whatsapp' => 'Lopende opdracht: WhatsApp',
      ],
      'ru' => [
        'title' => 'ВРЕМЕННО НЕ ПРИНИМАЕМ НОВЫЕ ЗАЯВКИ',
        'text' => 'Сейчас мы не принимаем новые заказы, записи и запросы на расчёт. Если у вас уже есть текущая работа с +QUECLIMA, вы можете связаться с нами обычным способом.',
        'call' => 'Текущий заказ: позвонить',
        'whatsapp' => 'Текущий заказ: WhatsApp',
      ],
      'no' => [
        'title' => 'MIDLERTIDIG STENGT FOR NYE OPPDRAG',
        'text' => 'Vi tar for tiden ikke imot nye oppdrag, bestillinger eller forespørsler om tilbud. Har du allerede et pågående oppdrag med +QUECLIMA, kan du kontakte oss via de vanlige kanalene.',
        'call' => 'Pågående oppdrag: ring',
        'whatsapp' => 'Pågående oppdrag: WhatsApp',
      ],
    ];

    return $copy[$lang] ?? $copy['es'];
  }
}

if (!function_exists('site_status_add_body_class')) {
  function site_status_add_body_class(string $html): string {
    return preg_replace_callback(
      '/<body\b([^>]*)>/i',
      static function (array $matches): string {
        $attrs = $matches[1] ?? '';
        if (preg_match('/\bclass=("|\')([^"\']*)\1/i', $attrs, $classMatch)) {
          $current = trim((string) ($classMatch[2] ?? ''));
          if (!preg_match('/(?:^|\s)booking-closed(?:\s|$)/', $current)) {
            $replacement = 'class="' . trim($current . ' booking-closed') . '"';
            $attrs = preg_replace('/\bclass=("|\')[^"\']*\1/i', $replacement, $attrs, 1) ?? $attrs;
          }
        } else {
          $attrs .= ' class="booking-closed"';
        }

        return '<body' . $attrs . '>';
      },
      $html,
      1
    ) ?? $html;
  }
}

if (!function_exists('site_status_transform_output')) {
  function site_status_transform_output(string $html, int $phase = 0): string {
    if ((bool) config('app.accepting_new_work', true)) {
      return $html;
    }

    // No tocar healthchecks, respuestas de error en texto ni otros payloads.
    if (stripos($html, '<html') === false || stripos($html, '</body>') === false) {
      return $html;
    }

    if (str_contains($html, 'data-site-status="booking-closed"')) {
      return $html;
    }

    $lang = (string) ($GLOBALS['current_lang'] ?? config('brand.default_lang', 'es'));
    $copy = site_status_copy($lang);

    $styles = <<<'HTML'
<style id="site-status-styles">
  .site-status-banner{
    margin-top:72px;
    position:relative;
    z-index:1035;
    background:#075ea8;
    color:#fff;
    border-bottom:1px solid rgba(255,255,255,.2);
    box-shadow:0 4px 16px rgba(0,0,0,.16);
    padding:1rem 1.25rem;
  }
  .site-status-banner__inner{
    max-width:1180px;
    margin:0 auto;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:1.25rem;
  }
  .site-status-banner__copy{min-width:0;}
  .site-status-banner__title{
    display:block;
    margin:0 0 .28rem;
    font-size:clamp(1.05rem,2vw,1.28rem);
    line-height:1.2;
    font-weight:800;
    letter-spacing:.035em;
  }
  .site-status-banner__text{
    margin:0;
    max-width:860px;
    font-size:.96rem;
    line-height:1.5;
    color:#fff;
  }
  .site-status-banner__actions{
    display:flex;
    flex:0 0 auto;
    gap:.55rem;
    flex-wrap:wrap;
    justify-content:flex-end;
  }
  .site-status-banner__action{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:42px;
    padding:.62rem .85rem;
    border:1px solid rgba(255,255,255,.82);
    border-radius:8px;
    color:#fff!important;
    background:rgba(255,255,255,.08);
    text-decoration:none!important;
    font-weight:700;
    font-size:.86rem;
    line-height:1.2;
  }
  .site-status-banner__action:hover,
  .site-status-banner__action:focus{
    background:#fff;
    color:#075ea8!important;
  }

  /* Con la agenda cerrada, se mantiene el contenido SEO pero se corta la captación. */
  body.booking-closed [data-bs-target="#quoteModal"],
  body.booking-closed #quoteModal,
  body.booking-closed .contact-form,
  body.booking-closed .budget-panel--final,
  body.booking-closed [data-ev="reviews_cta"]{
    display:none!important;
  }

  @media (max-width:991.98px){
    .site-status-banner{margin-top:68px;padding:.9rem 1rem;}
    .site-status-banner__inner{display:block;}
    .site-status-banner__actions{justify-content:flex-start;margin-top:.75rem;}
    .site-status-banner__action{flex:1 1 170px;}
  }
</style>
HTML;

    $banner = '<aside class="site-status-banner" data-site-status="booking-closed" role="status" aria-label="' . e($copy['title']) . '">' .
      '<div class="site-status-banner__inner">' .
        '<div class="site-status-banner__copy">' .
          '<strong class="site-status-banner__title">' . e($copy['title']) . '</strong>' .
          '<p class="site-status-banner__text">' . e($copy['text']) . '</p>' .
        '</div>' .
        '<div class="site-status-banner__actions">' .
          '<a class="site-status-banner__action js-track" data-ev="existing_job_call" href="tel:+34613026600">' . e($copy['call']) . '</a>' .
          '<a class="site-status-banner__action js-track" data-ev="existing_job_whatsapp" href="https://wa.me/34613026600" target="_blank" rel="noopener">' . e($copy['whatsapp']) . '</a>' .
        '</div>' .
      '</div>' .
    '</aside>';

    $html = site_status_add_body_class($html);

    if (stripos($html, '</head>') !== false) {
      $html = preg_replace('/<\/head>/i', $styles . "\n</head>", $html, 1) ?? $html;
    }

    if (stripos($html, '</header>') !== false) {
      $html = preg_replace('/<\/header>/i', '</header>' . "\n" . $banner, $html, 1) ?? $html;
    } else {
      $html = preg_replace('/<body([^>]*)>/i', '<body$1>' . "\n" . $banner, $html, 1) ?? $html;
    }

    return $html;
  }
}

if (!function_exists('site_status_register_output_buffer')) {
  function site_status_register_output_buffer(): void {
    static $registered = false;
    if ($registered || (bool) config('app.accepting_new_work', true)) {
      return;
    }

    $registered = true;
    ob_start('site_status_transform_output');
  }
}
