<?php
$image = $image ?? [];
$src = is_array($image) ? ($image['src'] ?? '') : '';
$alt = is_array($image) ? ($image['alt'] ?? '') : '';
$caption = is_array($image) ? ($image['caption'] ?? '') : '';
$eyebrow = $eyebrow ?? '';
$title = $title ?? '';
$titleId = $title_id ?? '';
$text = $text ?? '';
$reverse = !empty($reverse);
$hasImage = public_asset_exists(is_string($src) ? $src : null);
$classes = 'image-text-block' . ($reverse ? ' image-text-block--reverse' : '') . ($hasImage ? ' image-text-block--has-image' : ' image-text-block--fallback');
?>
<div class="<?= e($classes) ?>">
  <div class="image-text-block__media">
    <?php if ($hasImage): ?>
      <img src="<?= e((string) $src) ?>" alt="<?= e((string) $alt) ?>" width="1448" height="1086" loading="lazy" decoding="async">
    <?php else: ?>
      <div class="image-text-block__fallback" aria-hidden="true"></div>
    <?php endif; ?>
    <?php if ($caption !== ''): ?>
      <p class="image-text-block__caption"><?= $caption ?></p>
    <?php endif; ?>
  </div>
  <div class="image-text-block__content">
    <?php if ($eyebrow !== ''): ?>
      <p class="hub-kicker"><?= $eyebrow ?></p>
    <?php endif; ?>
    <?php if ($title !== ''): ?>
      <h2 class="section-title"<?= $titleId !== '' ? ' id="' . e((string) $titleId) . '"' : '' ?>><?= $title ?></h2>
    <?php endif; ?>
    <?php if ($text !== ''): ?>
      <p class="hub-muted"><?= $text ?></p>
    <?php endif; ?>
  </div>
</div>
