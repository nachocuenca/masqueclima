<?php
$service = $service ?? [];
$priorityZones = [
  ['Benidorm', '/es/aire-acondicionado-benidorm/'],
  ['Altea', '/es/aire-acondicionado-altea/'],
  ['Calpe', '/es/aire-acondicionado-calpe/'],
  ['Finestrat', '/es/aire-acondicionado-finestrat/'],
  ['La Nuc&iacute;a', '/es/aire-acondicionado-la-nucia/'],
];
?>
<?= hub_styles() ?>
<?= hub_intro((string) $service['h1'], (string) $service['subtitle'], hub_visual_options($service)) ?>
<section class="hub-section service-detail" aria-labelledby="service-overview">
  <div class="container">
    <?php if (!empty($service['support_image']) && is_array($service['support_image'])): ?>
      <?= render_partial('image_text_block', [
        'image' => $service['support_image'],
        'eyebrow' => 'Servicio',
        'title' => $service['intro_title'],
        'title_id' => 'service-overview',
        'text' => $service['intro'],
      ]) ?>
    <?php else: ?>
      <p class="hub-kicker">Servicio</p>
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
      <a class="btn btn-primary js-track" data-ev="<?= $service['cta_event'] ?>" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote" aria-controls="quoteModal">Pedir presupuesto</a>
      <a class="btn btn-outline-primary" href="/es/zonas/">Ver zonas de servicio</a>
    </div>
  </div>
</section>
<section class="hub-section alt service-process" aria-labelledby="service-process">
  <div class="container">
    <p class="hub-kicker">Proceso</p>
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
    <p class="hub-kicker">Zonas</p>
    <h2 class="section-title" id="service-areas">Zonas donde prestamos servicio</h2>
    <p class="hub-muted"><?= $service['zones_text'] ?></p>
    <div class="hub-links">
      <a class="hub-pill" href="/es/servicios/">Todos los servicios</a>
      <a class="hub-pill" href="/es/zonas/">Todas las zonas</a>
      <?php foreach ($priorityZones as [$name, $url]): ?>
        <a class="hub-pill" href="<?= $url ?>"><?= $name ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php if (!empty($service['faq'])): ?>
<section class="hub-section alt service-faq" aria-labelledby="service-faq">
  <div class="container">
    <p class="hub-kicker">Dudas habituales</p>
    <h2 class="section-title" id="service-faq">Preguntas frecuentes</h2>
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
