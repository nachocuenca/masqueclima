<?php
$guide   = $guide   ?? [];
$guideUi = $guideUi ?? [];
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
  </div>
</section>

<?php foreach ($guide['sections'] ?? [] as $i => $section): ?>
<section class="hub-section<?= ($i % 2 === 1) ? ' alt' : '' ?> guide-section" aria-labelledby="guide-section-<?= $i ?>">
  <div class="container">
    <h2 class="section-title" id="guide-section-<?= $i ?>"><?= $section['heading'] ?></h2>
    <?php foreach ($section['body'] as $item): ?>
      <?php if (is_array($item) && isset($item['bullets'])): ?>
        <ul class="guide-bullets">
          <?php foreach ($item['bullets'] as $bullet): ?>
            <li><?= $bullet ?></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="hub-muted"><?= $item ?></p>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</section>
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
