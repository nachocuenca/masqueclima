<?php
$heroImage = $hero_image ?? null;
$heroAlt = $hero_alt ?? '+QUECLIMA climatizaci&oacute;n en Alicante';
$fallbackImage = $fallback_image ?? '/assets/img/hero1.webp';
$visualSlot = $visual_slot ?? '';
$overlay = $overlay ?? 'soft';
$imagePosition = $image_position ?? 'center center';
$hasHeroImage = public_asset_exists(is_string($heroImage) ? $heroImage : null);
$hasFallbackImage = !$hasHeroImage && public_asset_exists(is_string($fallbackImage) ? $fallbackImage : null);
$heroClasses = 'hero hub-hero hero--compact ' . ($hasHeroImage ? 'hero--has-image' : ($hasFallbackImage ? 'hero--uses-fallback-image' : 'hero--visual-fallback'));
?>
<section class="<?= e(trim($heroClasses)) ?>" id="inicio" style="--hero-image-position:<?= e((string) $imagePosition) ?>"<?= $visualSlot !== '' ? ' data-visual-slot="' . e((string) $visualSlot) . '"' : '' ?>>
  <div class="hero-bg">
    <?php if ($hasHeroImage): ?>
      <img class="hero-img" src="<?= e((string) $heroImage) ?>" alt="<?= e((string) $heroAlt) ?>" width="1920" height="1080" fetchpriority="high" decoding="async">
    <?php elseif ($hasFallbackImage): ?>
      <img class="hero-img" src="<?= e((string) $fallbackImage) ?>" alt="<?= e((string) $heroAlt) ?>" width="1920" height="1080" fetchpriority="high" decoding="async">
    <?php else: ?>
      <div class="hero-visual-fallback" aria-hidden="true"></div>
    <?php endif; ?>
    <div class="hero-overlay hero-overlay--<?= e((string) $overlay) ?>" aria-hidden="true"></div>
  </div>
  <div class="container">
    <h1><?= $h1 ?? '' ?></h1>
    <p><?= $text ?? '' ?></p>
    <div class="cta-group">
      <a class="cta-button js-track" data-ev="cta_quote_hub" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote" aria-controls="quoteModal"><?= e(t('cta.quote', 'Pide presupuesto sin compromiso')) ?></a>
      <a class="cta-button cta-whatsapp js-track" data-ev="cta_whatsapp_hub" href="https://wa.me/34613026600" target="_blank" rel="noopener" aria-label="<?= e(t('cta.whatsapp', 'Escríbenos por WhatsApp')) ?>"><?= e(t('cta.whatsapp', 'Escríbenos por WhatsApp')) ?></a>
    </div>
  </div>
</section>
