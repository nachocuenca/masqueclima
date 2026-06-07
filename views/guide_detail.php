<?php
$guide   = $guide   ?? [];
$guideUi = $guideUi ?? [];
$sections = is_array($guide['sections'] ?? null) ? $guide['sections'] : [];
$quickSummary = is_array($guide['quick_summary'] ?? null) ? $guide['quick_summary'] : [];
$midCta = is_array($guide['mid_cta'] ?? null) ? $guide['mid_cta'] : [];
$midCtaAfter = (int)($midCta['after_section'] ?? 1);
?>
<?= hub_styles() ?>
<?= hub_intro(
    (string)($guide['h1'] ?? ''),
    (string)($guide['hero_subtitle'] ?? ''),
    [
        'hero_image'     => $guide['hero_image']    ?? null,
        'hero_alt'       => $guide['h1']            ?? '+QUECLIMA',
        'visual_slot'    => '',
        'overlay'        => 'soft',
        'image_position' => 'center center',
    ]
) ?>

<section class="hub-section guide-intro" aria-labelledby="guide-intro-heading">
  <div class="container">
    <p class="hub-kicker"><?= $guideUi['intro_kicker'] ?? 'Guía' ?></p>
    <h2 class="section-title" id="guide-intro-heading"><?= $guide['h1'] ?? '' ?></h2>
    <p class="hub-muted"><?= $guide['intro'] ?? '' ?></p>
    <?php if (!empty($quickSummary)): ?>
      <div class="guide-quick-summary" aria-label="Resumen rapido">
        <h3><?= $guide['quick_summary_title'] ?? 'Resumen r&aacute;pido' ?></h3>
        <ul>
          <?php foreach ($quickSummary as $item): ?>
            <li><?= $item ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <?php if (!empty($sections)): ?>
      <nav class="guide-toc" aria-label="Indice de la guia">
        <strong><?= $guide['toc_title'] ?? 'Indice de la guia' ?></strong>
        <ol>
          <?php foreach ($sections as $i => $section): ?>
            <li><a href="#guide-section-<?= $i ?>"><?= $section['heading'] ?? '' ?></a></li>
          <?php endforeach; ?>
        </ol>
      </nav>
    <?php endif; ?>
  </div>
</section>

<?php foreach ($sections as $i => $section): ?>
<section class="hub-section<?= ($i % 2 === 1) ? ' alt' : '' ?> guide-section" aria-labelledby="guide-section-<?= $i ?>">
  <div class="container">
    <h2 class="section-title" id="guide-section-<?= $i ?>"><?= $section['heading'] ?></h2>
    <?php foreach ($section['body'] as $item): ?>
      <?php if (is_array($item) && isset($item['subheading'])): ?>
        <h3 class="guide-subheading"><?= $item['subheading'] ?></h3>
        <?php if (!empty($item['text'])): ?>
          <p class="hub-muted"><?= $item['text'] ?></p>
        <?php endif; ?>
      <?php elseif (is_array($item) && isset($item['bullets'])): ?>
        <ul class="guide-bullets">
          <?php foreach ($item['bullets'] as $bullet): ?>
            <li><?= $bullet ?></li>
          <?php endforeach; ?>
        </ul>
      <?php elseif (is_array($item) && isset($item['callout'])): ?>
        <aside class="guide-callout">
          <?php if (!empty($item['callout']['title'])): ?>
            <strong><?= $item['callout']['title'] ?></strong>
          <?php endif; ?>
          <?php if (!empty($item['callout']['text'])): ?>
            <p><?= $item['callout']['text'] ?></p>
          <?php endif; ?>
        </aside>
      <?php elseif (is_array($item) && isset($item['table'])): ?>
        <div class="guide-table-wrap">
          <table class="guide-table">
            <?php if (!empty($item['table']['headers'])): ?>
              <thead>
                <tr>
                  <?php foreach ($item['table']['headers'] as $header): ?>
                    <th><?= $header ?></th>
                  <?php endforeach; ?>
                </tr>
              </thead>
            <?php endif; ?>
            <tbody>
              <?php foreach ($item['table']['rows'] ?? [] as $row): ?>
                <tr>
                  <?php foreach ($row as $cell): ?>
                    <td><?= $cell ?></td>
                  <?php endforeach; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="hub-muted"><?= $item ?></p>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</section>
<?php if (!empty($midCta) && $i === $midCtaAfter): ?>
<section class="hub-section guide-mid-cta-section" aria-labelledby="guide-mid-cta-heading">
  <div class="container">
    <div class="guide-mid-cta">
      <div>
        <p class="hub-kicker"><?= $midCta['kicker'] ?? 'Presupuesto' ?></p>
        <h2 class="section-title" id="guide-mid-cta-heading"><?= $midCta['title'] ?? 'Pide asesoramiento' ?></h2>
        <p class="hub-muted"><?= $midCta['text'] ?? 'Cu&eacute;ntanos tu caso y te orientamos con una propuesta clara.' ?></p>
      </div>
      <a class="btn btn-primary js-track" data-ev="<?= e($midCta['event'] ?? 'cta_quote_guide_mid') ?>" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote"><?= $midCta['label'] ?? 'Pedir presupuesto' ?></a>
    </div>
  </div>
</section>
<?php endif; ?>
<?php endforeach; ?>

<?php if (!empty($guide['faq'])): ?>
<section class="hub-section alt guide-faq" aria-labelledby="guide-faq-heading">
  <div class="container">
    <p class="hub-kicker"><?= $guideUi['faq_kicker'] ?? 'FAQ' ?></p>
    <h2 class="section-title" id="guide-faq-heading"><?= $guideUi['faq_h2'] ?? 'Preguntas frecuentes' ?></h2>
    <div class="service-faq-grid">
      <?php foreach ($guide['faq'] as $faq): ?>
        <article class="service-faq-card">
          <h3><?= $faq['q'] ?></h3>
          <p><?= $faq['a'] ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($guide['related_services'])): ?>
<section class="hub-section guide-related-services" aria-labelledby="guide-svc-heading">
  <div class="container">
    <p class="hub-kicker"><?= $guideUi['services_kicker'] ?? 'Servicios' ?></p>
    <h2 class="section-title" id="guide-svc-heading"><?= $guideUi['services_h2'] ?? 'Servicios relacionados' ?></h2>
    <div class="hub-links">
      <?php foreach ($guide['related_services'] as $svc): ?>
        <a class="hub-pill" href="<?= e($svc['url']) ?>"><?= $svc['label'] ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($guide['related_areas'])): ?>
<section class="hub-section alt guide-related-areas" aria-labelledby="guide-areas-heading">
  <div class="container">
    <p class="hub-kicker"><?= $guideUi['areas_kicker'] ?? 'Zonas' ?></p>
    <h2 class="section-title" id="guide-areas-heading"><?= $guideUi['areas_h2'] ?? 'Zonas donde trabajamos' ?></h2>
    <div class="hub-links">
      <?php foreach ($guide['related_areas'] as $area): ?>
        <a class="hub-pill" href="<?= e($area['url']) ?>"><?= $area['name'] ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
