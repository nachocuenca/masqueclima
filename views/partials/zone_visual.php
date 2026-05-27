<?php $variant = $variant ?? 'full'; ?>
<?php if ($variant === 'mini'): ?>
<div class="mini-map-card" aria-label="Esquema de cobertura en la Marina Baixa">
  <svg class="marina-map" viewBox="0 0 420 260" role="img" aria-labelledby="mini-map-title">
    <title id="mini-map-title">Cobertura principal en la Marina Baixa</title>
    <path class="sea" d="M255 0h165v260H220c34-43 43-80 34-120-8-36-2-82 1-140Z"></path>
    <path class="coast" d="M260 18c-32 36-24 69-12 102 13 36 6 75-28 116"></path>
    <path class="route" d="M78 172c45-32 83-47 136-56 40-7 70-23 98-55"></path>
    <g class="map-zone" tabindex="0">
      <circle class="zone-shape" cx="200" cy="150" r="32"></circle>
      <text class="map-label" x="200" y="154" text-anchor="middle">Benidorm</text>
    </g>
    <g class="map-zone" tabindex="0">
      <circle class="zone-shape" cx="245" cy="94" r="28"></circle>
      <text class="map-label" x="245" y="98" text-anchor="middle">Altea</text>
    </g>
    <g class="map-zone" tabindex="0">
      <circle class="zone-shape" cx="312" cy="58" r="28"></circle>
      <text class="map-label" x="312" y="62" text-anchor="middle">Calpe</text>
    </g>
    <g class="map-zone" tabindex="0">
      <circle class="zone-shape" cx="145" cy="128" r="28"></circle>
      <text class="map-label" x="145" y="132" text-anchor="middle">Finestrat</text>
    </g>
    <g class="map-zone" tabindex="0">
      <circle class="zone-shape" cx="178" cy="82" r="30"></circle>
      <text class="map-label" x="178" y="86" text-anchor="middle">La Nuc&iacute;a</text>
    </g>
  </svg>
</div>
<?php else: ?>
<div class="zone-map-card">
  <svg class="marina-map" viewBox="0 0 520 340" role="img" aria-labelledby="zone-map-title zone-map-desc">
    <title id="zone-map-title">Mapa esquem&aacute;tico de servicio en Marina Baixa</title>
    <desc id="zone-map-desc">Las zonas principales se iluminan al pasar el cursor.</desc>
    <path class="sea" d="M350 0h170v340H312c48-63 59-112 47-164-12-49-13-104-9-176Z"></path>
    <path class="coast" d="M350 20c-38 45-35 82-18 130 17 51 5 103-38 166"></path>
    <path class="route" d="M88 222c62-44 114-66 184-78 48-8 92-33 130-78"></path>
    <path class="route" d="M126 104c44 24 93 35 151 34"></path>
    <g class="map-zone" tabindex="0">
      <path class="zone-shape" d="M237 178c31-7 58 12 60 43 1 31-24 54-55 49-27-5-47-28-43-55 3-20 17-32 38-37Z"></path>
      <text class="map-label" x="249" y="226" text-anchor="middle">Benidorm</text>
    </g>
    <g class="map-zone" tabindex="0">
      <path class="zone-shape" d="M296 105c31-12 61 4 67 35 5 31-17 57-49 56-28-1-51-21-52-48-1-20 12-35 34-43Z"></path>
      <text class="map-label" x="313" y="153" text-anchor="middle">Altea</text>
    </g>
    <g class="map-zone" tabindex="0">
      <path class="zone-shape" d="M382 50c26-11 54 3 61 30 7 28-12 53-41 54-26 1-49-17-51-41-2-19 10-34 31-43Z"></path>
      <text class="map-label" x="397" y="91" text-anchor="middle">Calpe</text>
    </g>
    <g class="map-zone" tabindex="0">
      <path class="zone-shape" d="M150 158c29-14 62-1 72 29 10 29-8 58-39 62-28 4-54-12-61-38-5-21 6-41 28-53Z"></path>
      <text class="map-label" x="172" y="207" text-anchor="middle">Finestrat</text>
    </g>
    <g class="map-zone" tabindex="0">
      <path class="zone-shape" d="M197 77c29-12 59 2 68 31 8 28-11 55-41 57-27 2-52-16-57-42-3-20 8-37 30-46Z"></path>
      <text class="map-label" x="218" y="119" text-anchor="middle">La Nuc&iacute;a</text>
    </g>
    <text class="map-label" x="440" y="292" text-anchor="middle">Costa Blanca</text>
  </svg>
</div>
<?php endif; ?>
