<?php
$service = $service ?? [];
// $priorityZones, $otherServices, $servicesHubUrl, $zonesHubUrl, $serviceUi
// are injected by render_service_detail_body(); these are ES fallbacks only.
if (!isset($priorityZones)) {
  $priorityZones = [
    ['Benidorm',         '/es/aire-acondicionado-benidorm/'],
    ['Altea',            '/es/aire-acondicionado-altea/'],
    ['Calpe',            '/es/aire-acondicionado-calpe/'],
    ['Finestrat',        '/es/aire-acondicionado-finestrat/'],
    ['La Nuc&iacute;a',  '/es/aire-acondicionado-la-nucia/'],
  ];
}
if (!isset($otherServices)) {
  $otherServices = [
    ['Instalaci&oacute;n de aire acondicionado', '/es/servicios/instalacion-aire-acondicionado/'],
    ['Mantenimiento de climatizaci&oacute;n',    '/es/servicios/mantenimiento-climatizacion/'],
    ['Reparaci&oacute;n de aire acondicionado',  '/es/servicios/reparacion-aire-acondicionado/'],
    ['Aerotermia y bomba de calor',              '/es/servicios/aerotermia-bomba-calor/'],
    ['Energ&iacute;a solar t&eacute;rmica',      '/es/servicios/energia-solar-termica/'],
  ];
}
if (!isset($servicesHubUrl)) { $servicesHubUrl = '/es/servicios/'; }
if (!isset($zonesHubUrl))    { $zonesHubUrl    = '/es/zonas/'; }
if (!isset($serviceUi)) {
  $serviceUi = [
    'service_kicker' => 'Servicio', 'cta_quote' => 'Pedir presupuesto',
    'cta_zones'      => 'Ver zonas de servicio', 'process_kicker' => 'Proceso',
    'zones_kicker'   => 'Zonas', 'zones_title' => 'Zonas donde prestamos servicio',
    'all_services'   => 'Todos los servicios', 'all_zones' => 'Todas las zonas',
    'other_kicker'   => 'Otros servicios', 'related_title' => 'Servicios relacionados',
    'faq_kicker'     => 'Dudas habituales', 'faq_title' => 'Preguntas frecuentes',
  ];
}
$relatedGuideUrl = $relatedGuideUrl ?? null;
$relatedGuideTitle = $relatedGuideTitle ?? '';
$relatedGuideLabel = $relatedGuideLabel ?? 'Related guide';
$currentPath = $service['path'] ?? '';
$relatedServices = array_filter($otherServices, fn($s) => $s[1] !== $currentPath);
?>
<?= hub_styles() ?>
<?= hub_intro((string) $service['h1'], (string) $service['subtitle'], hub_visual_options($service)) ?>
<section class="hub-section service-detail" aria-labelledby="service-overview">
  <div class="container">
    <?php if (!empty($service['support_image']) && is_array($service['support_image'])): ?>
      <?= render_partial('image_text_block', [
        'image' => $service['support_image'],
        'eyebrow' => $serviceUi['service_kicker'],
        'title' => $service['intro_title'],
        'title_id' => 'service-overview',
        'text' => $service['intro'],
      ]) ?>
    <?php else: ?>
      <p class="hub-kicker"><?= $serviceUi['service_kicker'] ?></p>
      <h2 class="section-title" id="service-overview"><?= $service['intro_title'] ?></h2>
      <p class="hub-muted"><?= $service['intro'] ?></p>
    <?php endif; ?>
    <div class="service-detail-grid">
      <article class="service-detail-card">
        <h3><?= $service['bullets_title'] ?></h3>
        <ul>
          <?php foreach ($service['bullets'] as $item): ?>
            <li><?= $item ?></li>
          <?php endforeach; ?>
        </ul>
      </article>
      <article class="service-detail-card">
        <h3><?= $service['when_title'] ?></h3>
        <ul>
          <?php foreach ($service['when_items'] as $item): ?>
            <li><?= $item ?></li>
          <?php endforeach; ?>
        </ul>
      </article>
    </div>
    <div class="hub-cta service-inline-cta">
      <a class="btn btn-primary js-track" data-ev="<?= $service['cta_event'] ?>" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote" aria-controls="quoteModal"><?= $serviceUi['cta_quote'] ?></a>
      <a class="btn btn-outline-primary" href="<?= $zonesHubUrl ?>"><?= $serviceUi['cta_zones'] ?></a>
    </div>
  </div>
</section>
<section class="hub-section alt service-process" aria-labelledby="service-process">
  <div class="container">
    <p class="hub-kicker"><?= $serviceUi['process_kicker'] ?></p>
    <h2 class="section-title" id="service-process"><?= $service['process_title'] ?></h2>
    <div class="service-step-grid">
      <?php foreach ($service['process_items'] as $index => $item): ?>
        <article class="service-step">
          <span class="service-step-number"><?= $index + 1 ?></span>
          <p><?= $item ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<section class="hub-section service-areas" aria-labelledby="service-areas">
  <div class="container">
    <p class="hub-kicker"><?= $serviceUi['zones_kicker'] ?></p>
    <h2 class="section-title" id="service-areas"><?= $serviceUi['zones_title'] ?></h2>
    <p class="hub-muted"><?= $service['zones_text'] ?></p>
    <div class="hub-links">
      <a class="hub-pill" href="<?= $servicesHubUrl ?>"><?= $serviceUi['all_services'] ?></a>
      <a class="hub-pill" href="<?= $zonesHubUrl ?>"><?= $serviceUi['all_zones'] ?></a>
      <?php if (!empty($guidesUrl)): ?>
        <a class="hub-pill" href="<?= e($guidesUrl) ?>"><?= $guidesLabel ?></a>
      <?php endif; ?>
      <?php foreach ($priorityZones as [$name, $url]): ?>
        <a class="hub-pill" href="<?= $url ?>"><?= $name ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php if (!empty($relatedGuideUrl)): ?>
<section class="hub-section service-related-guide" aria-labelledby="service-related-guide">
  <div class="container">
    <p class="hub-kicker"><?= $relatedGuideLabel ?></p>
    <h2 class="section-title" id="service-related-guide"><?= $relatedGuideTitle ?></h2>
    <div class="hub-links">
      <a class="hub-pill" href="<?= e($relatedGuideUrl) ?>"><?= $relatedGuideTitle ?></a>
    </div>
  </div>
</section>
<?php endif; ?>
<?php if (!empty($relatedServices)): ?>
<section class="hub-section alt service-related" aria-labelledby="service-related">
  <div class="container">
    <p class="hub-kicker"><?= $serviceUi['other_kicker'] ?></p>
    <h2 class="section-title" id="service-related"><?= $serviceUi['related_title'] ?></h2>
    <div class="hub-links">
      <?php foreach ($relatedServices as [$name, $url]): ?>
        <a class="hub-pill" href="<?= $url ?>"><?= $name ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
<?php if (!empty($service['faq'])): ?>
<section class="hub-section alt service-faq" aria-labelledby="service-faq">
  <div class="container">
    <p class="hub-kicker"><?= $serviceUi['faq_kicker'] ?></p>
    <h2 class="section-title" id="service-faq"><?= $serviceUi['faq_title'] ?></h2>
    <div class="service-faq-grid">
      <?php foreach ($service['faq'] as $faq): ?>
        <article class="service-faq-card">
          <h3><?= $faq['q'] ?></h3>
          <p><?= $faq['a'] ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
