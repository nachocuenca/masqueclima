<?php $variant = $variant ?? 'full'; ?>
<?php
/*
 * Marina Baixa / Costa Blanca Norte — SVG map
 * Coordinate system (shared by both variants):
 *   ViewBox: 0 0 600 420
 *   lon [-0.28, +0.07]  → x = (lon + 0.28) / 0.35 * 600
 *   lat [38.73, 38.47]  → y = (38.73 - lat)  / 0.26 * 420
 *
 * Key positions (approximate real coordinates):
 *   Benidorm      252,310   Villajoyosa  83,354
 *   Altea         390,212   Calpe       552,139
 *   Finestrat     174,282   La Nucía    263,177
 *   Alfaz del Pi  324,244   Albir       343,233
 *   Polop         233,155   Callosa     281,110
 *   Tàrbena       357, 42   Orxeta      121,264
 *   Sella          78,231   Relleu       41,279
 *   Benimantell   198, 92   Guadalest   149, 69
 *   Confrides     157, 36   Beniardà    167, 80
 *   Bolulla       255, 72   Benifato    191, 62
 */
$land   = 'M 0 0 L 600 0 L 600 112'
        . ' C 590 124 572 134 552 139'
        . ' C 522 145 496 153 470 166'
        . ' C 440 181 414 196 390 212'
        . ' C 376 220 362 228 350 237'
        . ' C 334 249 318 263 302 275'
        . ' C 282 290 262 306 245 310'
        . ' C 226 314 210 316 185 318'
        . ' C 140 322 90 328 83 354'
        . ' C 76 378 70 400 60 420'
        . ' L 0 420 Z';
$coast  = 'M 60 420'
        . ' C 70 400 76 378 83 354'
        . ' C 90 328 140 322 185 318'
        . ' C 210 316 226 314 245 310'
        . ' C 262 306 282 290 302 275'
        . ' C 318 263 334 249 350 237'
        . ' C 362 228 376 220 390 212'
        . ' C 414 196 440 181 470 166'
        . ' C 496 153 522 145 552 139'
        . ' C 572 134 590 124 600 112';
$mtn    = 'M 0 0 L 380 0 L 355 58 L 290 82 L 242 112'
        . ' L 198 148 L 162 190 L 130 242 L 104 298'
        . ' L 78 355 L 44 400 L 0 420 Z';
?>
<?php if ($variant === 'mini'): ?>
<div class="mini-map-card" aria-label="Cobertura principal en la Marina Baixa, Costa Blanca norte">
  <svg class="mc-map-svg" viewBox="120 100 450 295" role="img"
       aria-labelledby="mc-mini-title mc-mini-desc">
    <title id="mc-mini-title">Mapa de cobertura — Marina Baixa, Costa Blanca Norte</title>
    <desc id="mc-mini-desc">Localidades costeras e interiores donde Masqueclima presta servicio.</desc>

    <!-- Base geography -->
    <rect width="600" height="420" class="mc-sea"/>
    <path class="mc-land" d="<?php echo $land; ?>"/>
    <path class="mc-mtn"  d="<?php echo $mtn;  ?>"/>
    <path class="mc-coast-line" d="<?php echo $coast; ?>"/>

    <!-- Sea label -->
    <text class="mc-sea-label" x="565" y="370" text-anchor="end">Mar Mediterr&#225;neo</text>

    <!-- Localidades principales -->
    <a href="/es/aire-acondicionado-benidorm/" class="mc-zone mc-zone-main" aria-label="Aire acondicionado en Benidorm">
      <circle class="mc-dot mc-dot-main" cx="252" cy="310" r="11"/>
      <text class="mc-label mc-label-main" x="268" y="329">Benidorm</text>
    </a>

    <a href="/es/aire-acondicionado-finestrat/" class="mc-zone mc-zone-primary" aria-label="Aire acondicionado en Finestrat">
      <circle class="mc-dot" cx="174" cy="282" r="7"/>
      <text class="mc-label mc-label-visible" x="167" y="278" text-anchor="end">Finestrat</text>
    </a>

    <a href="/es/aire-acondicionado-la-nucia/" class="mc-zone mc-zone-primary" aria-label="Aire acondicionado en La Nuc&#237;a">
      <circle class="mc-dot" cx="263" cy="177" r="7"/>
      <text class="mc-label mc-label-visible" x="256" y="174" text-anchor="end">La Nuc&#237;a</text>
    </a>

    <a href="/es/aire-acondicionado-alfaz-del-pi/" class="mc-zone mc-zone-primary" aria-label="Aire acondicionado en Alfaz del Pi">
      <circle class="mc-dot" cx="324" cy="244" r="7"/>
      <text class="mc-label mc-label-visible" x="317" y="241" text-anchor="end">Alfaz del Pi</text>
    </a>

    <a href="/es/aire-acondicionado-altea/" class="mc-zone mc-zone-primary" aria-label="Aire acondicionado en Altea">
      <circle class="mc-dot" cx="390" cy="212" r="7"/>
      <text class="mc-label mc-label-visible" x="390" y="203" text-anchor="middle">Altea</text>
    </a>

    <a href="/es/aire-acondicionado-calpe/" class="mc-zone mc-zone-primary" aria-label="Aire acondicionado en Calpe">
      <circle class="mc-dot" cx="552" cy="139" r="7"/>
      <text class="mc-label mc-label-visible" x="544" y="132" text-anchor="end">Calpe</text>
    </a>
  </svg>
</div>
<?php else: ?>
<div class="zone-map-card" aria-label="Mapa de cobertura en la Marina Baixa y Costa Blanca norte">
  <svg class="mc-map-svg mc-map-full" viewBox="0 0 600 420" role="img"
       aria-labelledby="mc-full-title mc-full-desc">
    <title id="mc-full-title">Mapa de servicio — Marina Baixa, Costa Blanca Norte</title>
    <desc id="mc-full-desc">Localidades de la Marina Baixa donde Masqueclima instala y mantiene climatizaci&#243;n. Pasa el rat&#243;n para explorar.</desc>

    <!-- Base geography -->
    <rect width="600" height="420" class="mc-sea"/>
    <path class="mc-land" d="<?php echo $land; ?>"/>
    <path class="mc-mtn"  d="<?php echo $mtn;  ?>"/>
    <path class="mc-coast-line" d="<?php echo $coast; ?>"/>

    <!-- Decorative mountain peaks (interior sierra) -->
    <g class="mc-peaks" aria-hidden="true">
      <path d="M 128 97 L 139 74 L 150 97 Z"/>
      <path d="M 152 83 L 165 57 L 178 83 Z"/>
      <path d="M 108 130 L 119 108 L 130 130 Z"/>
      <path d="M 172 68 L 182 47 L 192 68 Z"/>
      <path d="M 84 182 L 94 162 L 104 182 Z"/>
      <path d="M 60 238 L 70 218 L 80 238 Z"/>
    </g>

    <!-- Region labels -->
    <text class="mc-sea-label" x="555" y="365" text-anchor="end">Mar Mediterr&#225;neo</text>
    <text class="mc-region-label" x="480" y="112" text-anchor="end">Costa Blanca Norte</text>

    <!-- ── SECONDARY localities (rendered first, below primaries) ── -->

    <a href="/es/aire-acondicionado-relleu/" class="mc-zone mc-zone-secondary" aria-label="Relleu">
      <circle class="mc-dot-sm" cx="41" cy="279" r="5"/>
      <text class="mc-label mc-label-hover" x="50" y="282">Relleu</text>
    </a>

    <a href="/es/aire-acondicionado-sella/" class="mc-zone mc-zone-secondary" aria-label="Sella">
      <circle class="mc-dot-sm" cx="78" cy="231" r="5"/>
      <text class="mc-label mc-label-hover" x="87" y="234">Sella</text>
    </a>

    <a href="/es/aire-acondicionado-orxeta/" class="mc-zone mc-zone-secondary" aria-label="Orxeta">
      <circle class="mc-dot-sm" cx="121" cy="264" r="5"/>
      <text class="mc-label mc-label-hover" x="130" y="267">Orxeta</text>
    </a>

    <a href="/es/aire-acondicionado-guadalest/" class="mc-zone mc-zone-secondary" aria-label="Guadalest">
      <circle class="mc-dot-sm" cx="149" cy="69" r="5"/>
      <text class="mc-label mc-label-hover" x="141" y="65" text-anchor="end">Guadalest</text>
    </a>

    <a href="/es/aire-acondicionado-confrides/" class="mc-zone mc-zone-secondary" aria-label="Confrides">
      <circle class="mc-dot-sm" cx="157" cy="36" r="5"/>
      <text class="mc-label mc-label-hover" x="166" y="34">Confrides</text>
    </a>

    <a href="/es/aire-acondicionado-beniarda/" class="mc-zone mc-zone-secondary" aria-label="Beniard&#224;">
      <circle class="mc-dot-sm" cx="167" cy="80" r="5"/>
      <text class="mc-label mc-label-hover" x="176" y="83">Beniard&#224;</text>
    </a>

    <a href="/es/aire-acondicionado-benifato/" class="mc-zone mc-zone-secondary" aria-label="Benifato">
      <circle class="mc-dot-sm" cx="191" cy="62" r="5"/>
      <text class="mc-label mc-label-hover" x="200" y="58">Benifato</text>
    </a>

    <a href="/es/aire-acondicionado-benimantell/" class="mc-zone mc-zone-secondary" aria-label="Benimantell">
      <circle class="mc-dot-sm" cx="198" cy="92" r="5"/>
      <text class="mc-label mc-label-hover" x="207" y="95">Benimantell</text>
    </a>

    <a href="/es/aire-acondicionado-bolulla/" class="mc-zone mc-zone-secondary" aria-label="Bolulla">
      <circle class="mc-dot-sm" cx="255" cy="72" r="5"/>
      <text class="mc-label mc-label-hover" x="264" y="75">Bolulla</text>
    </a>

    <a href="/es/aire-acondicionado-tarbena/" class="mc-zone mc-zone-secondary" aria-label="T&#224;rbena">
      <circle class="mc-dot-sm" cx="357" cy="42" r="5"/>
      <text class="mc-label mc-label-hover" x="357" y="55" text-anchor="middle">T&#224;rbena</text>
    </a>

    <a href="/es/aire-acondicionado-polop/" class="mc-zone mc-zone-secondary" aria-label="Polop">
      <circle class="mc-dot-sm" cx="233" cy="155" r="5"/>
      <text class="mc-label mc-label-hover" x="224" y="151" text-anchor="end">Polop</text>
    </a>

    <a href="/es/aire-acondicionado-callosa-den-sarria/" class="mc-zone mc-zone-secondary" aria-label="Callosa d&#39;en Sarri&#224;">
      <circle class="mc-dot-sm" cx="281" cy="110" r="5"/>
      <text class="mc-label mc-label-hover" x="290" y="113">Callosa</text>
    </a>

    <a href="/es/aire-acondicionado-albir/" class="mc-zone mc-zone-secondary" aria-label="Albir">
      <circle class="mc-dot-sm" cx="343" cy="233" r="5"/>
      <text class="mc-label mc-label-hover" x="352" y="243">Albir</text>
    </a>

    <!-- ── PRIMARY localities ── -->

    <a href="/es/aire-acondicionado-villajoyosa/" class="mc-zone mc-zone-primary" aria-label="Aire acondicionado en Villajoyosa">
      <circle class="mc-dot" cx="83" cy="354" r="7"/>
      <text class="mc-label mc-label-visible" x="75" y="368" text-anchor="end">Villajoyosa</text>
    </a>

    <a href="/es/aire-acondicionado-finestrat/" class="mc-zone mc-zone-primary" aria-label="Aire acondicionado en Finestrat">
      <circle class="mc-dot" cx="174" cy="282" r="7"/>
      <text class="mc-label mc-label-visible" x="167" y="278" text-anchor="end">Finestrat</text>
    </a>

    <a href="/es/aire-acondicionado-la-nucia/" class="mc-zone mc-zone-primary" aria-label="Aire acondicionado en La Nuc&#237;a">
      <circle class="mc-dot" cx="263" cy="177" r="7"/>
      <text class="mc-label mc-label-visible" x="256" y="174" text-anchor="end">La Nuc&#237;a</text>
    </a>

    <a href="/es/aire-acondicionado-alfaz-del-pi/" class="mc-zone mc-zone-primary" aria-label="Aire acondicionado en Alfaz del Pi">
      <circle class="mc-dot" cx="324" cy="244" r="7"/>
      <text class="mc-label mc-label-visible" x="317" y="241" text-anchor="end">Alfaz del Pi</text>
    </a>

    <a href="/es/aire-acondicionado-altea/" class="mc-zone mc-zone-primary" aria-label="Aire acondicionado en Altea">
      <circle class="mc-dot" cx="390" cy="212" r="7"/>
      <text class="mc-label mc-label-visible" x="390" y="203" text-anchor="middle">Altea</text>
    </a>

    <a href="/es/aire-acondicionado-calpe/" class="mc-zone mc-zone-primary" aria-label="Aire acondicionado en Calpe">
      <circle class="mc-dot" cx="552" cy="139" r="7"/>
      <text class="mc-label mc-label-visible" x="544" y="132" text-anchor="end">Calpe</text>
    </a>

    <!-- Benidorm — base principal, rendered last (on top) -->
    <a href="/es/aire-acondicionado-benidorm/" class="mc-zone mc-zone-main" aria-label="Aire acondicionado en Benidorm — sede principal">
      <circle class="mc-dot mc-dot-main" cx="252" cy="310" r="11"/>
      <text class="mc-label mc-label-main" x="268" y="329">Benidorm</text>
    </a>
  </svg>
</div>
<?php endif; ?>
