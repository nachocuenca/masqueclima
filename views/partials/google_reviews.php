<?php
$lang = $lang ?? ($GLOBALS['current_lang'] ?? 'es');
$contentFile = __DIR__ . '/../../app/content/google_reviews.php';
$payload = is_file($contentFile) ? require $contentFile : [];
if (!is_array($payload)) {
  return;
}

$rating = trim((string)($payload['rating'] ?? ''));
$reviewCount = (int)($payload['review_count'] ?? 0);
$googleUrl = trim((string)($payload['google_url'] ?? ''));
$updatedAt = trim((string)($payload['updated_at'] ?? ''));
$rawReviews = $payload['reviews'] ?? [];

if ($rating === '' || $reviewCount <= 0 || $googleUrl === '') {
  return;
}

$reviews = [];
if (is_array($rawReviews)) {
  foreach ($rawReviews as $item) {
    if (!is_array($item)) {
      continue;
    }
    $name = trim((string)($item['name'] ?? ''));
    $date = trim((string)($item['date'] ?? ''));
    $meta = trim((string)($item['meta'] ?? ''));
    $text = trim((string)($item['text'] ?? ''));
    $stars = max(1, min(5, (int)($item['stars'] ?? 0)));
    if ($name === '' || $text === '' || $stars < 1) {
      continue;
    }
    $reviews[] = [
      'name' => $name,
      'date' => $date,
      'meta' => $meta,
      'text' => $text,
      'stars' => $stars,
      'has_photo' => (bool)($item['has_photo'] ?? false),
    ];
  }
}

$initialsFor = static function (string $name): string {
  $words = preg_split('/\s+/u', trim($name)) ?: [];
  $letters = [];
  foreach ($words as $word) {
    if (preg_match('/[\p{L}\p{N}]/u', $word, $match) === 1) {
      $letters[] = $match[0];
    }
  }

  if (count($letters) >= 2) {
    return strtoupper($letters[0] . $letters[count($letters) - 1]);
  }

  return strtoupper($letters[0] ?? 'G');
};

$hasCards = count($reviews) > 0;
$sectionId = 'google-reviews-' . substr(md5($lang . '|' . $googleUrl), 0, 8);
$summaryLabel = (string)t('google_reviews.summary_label', 'Resumen de reseñas de Google');
$starsLabel = (string)t('google_reviews.stars_label', '%s de 5 estrellas');
$withPhotosLabel = (string)t('google_reviews.with_photos', 'con fotos');
$sourceTagLabel = (string)t('google_reviews.tag', 'Reseña de Google');
$slideLabel = (string)t('google_reviews.slide_label', 'Grupo de reseñas %s');
?>
<section class="google-reviews-section" aria-labelledby="<?php echo e($sectionId); ?>">
  <div class="container google-reviews-shell">
    <p class="google-reviews-eyebrow mb-2"><?php echo e(t('google_reviews.eyebrow', 'Clientes que ya confían en nosotros')); ?></p>
    <h2 class="google-reviews-title" id="<?php echo e($sectionId); ?>"><?php echo e(t('google_reviews.title', 'Opiniones de nuestros clientes')); ?></h2>

    <div class="google-reviews-summary" role="group" aria-label="<?php echo e($summaryLabel); ?>">
      <div class="google-score-wrap">
        <strong class="google-score-word"><?php echo e(t('google_reviews.excellent', 'Excelente')); ?></strong>
        <div class="google-stars" aria-hidden="true">★★★★★</div>
        <div class="google-score-meta">
          <span class="google-score-value"><?php echo e($rating); ?></span>
          <span class="google-score-sep">·</span>
          <span class="google-score-count"><?php echo e(sprintf((string)t('google_reviews.based_on', 'A base de %s reseñas'), number_format($reviewCount, 0, ',', '.'))); ?></span>
        </div>
      </div>

      <div class="google-review-summary-actions">
        <span class="google-badge" aria-hidden="true"><span class="google-badge-g">G</span>oogle</span>
        <span class="google-reviews-verified"><?php echo e(t('google_reviews.verified', 'Reseñas verificadas en Google')); ?></span>
        <a class="btn btn-outline-primary google-reviews-link" href="<?php echo e($googleUrl); ?>" target="_blank" rel="noopener noreferrer"><?php echo e(t('google_reviews.link', 'Ver todas las reseñas en Google')); ?></a>
      </div>
    </div>

    <?php if ($hasCards): ?>
      <div class="google-review-carousel" data-google-reviews-carousel data-visible="3" data-interval="6500">
        <div class="google-review-grid">
          <?php foreach ($reviews as $idx => $review): ?>
            <?php
              $isLocalGuide = stripos($review['meta'], 'Local Guide') !== false;
              $tagParts = [];
              if ($isLocalGuide) {
                $tagParts[] = 'Local Guide';
              }
              if ($review['has_photo']) {
                $tagParts[] = $withPhotosLabel;
              }
              $tagLabel = count($tagParts) > 0 ? implode(' · ', $tagParts) : $sourceTagLabel;
            ?>
            <article class="google-review-card<?php echo $idx >= 3 ? ' google-review-card--extra' : ''; ?>" data-review-index="<?php echo e((string)$idx); ?>">
              <div class="google-review-head">
                <div class="google-review-person">
                  <span class="google-review-avatar" aria-hidden="true"><?php echo e($initialsFor($review['name'])); ?></span>
                  <div class="google-review-identity">
                    <strong class="google-review-name"><?php echo e($review['name']); ?></strong>
                    <?php if ($review['date'] !== ''): ?>
                      <p class="google-review-date"><?php echo e($review['date']); ?></p>
                    <?php endif; ?>
                    <?php if ($review['meta'] !== ''): ?>
                      <p class="google-review-meta"><?php echo e($review['meta']); ?></p>
                    <?php endif; ?>
                  </div>
                </div>
                <span class="google-review-tag"><?php echo e($tagLabel); ?></span>
              </div>
              <div class="google-stars" aria-label="<?php echo e(sprintf($starsLabel, (string)$review['stars'])); ?>"><?php echo e(str_repeat('★', $review['stars']) . str_repeat('☆', 5 - $review['stars'])); ?></div>
              <p class="google-review-text"><?php echo e($review['text']); ?></p>
            </article>
          <?php endforeach; ?>
        </div>
        <div class="google-review-dots" data-dot-label="<?php echo e($slideLabel); ?>"></div>
      </div>
    <?php endif; ?>

    <?php if ($updatedAt !== ''): ?>
      <p class="google-reviews-updated mb-0"><?php echo e(sprintf((string)t('google_reviews.updated', 'Actualizado: %s'), $updatedAt)); ?></p>
    <?php endif; ?>
  </div>
</section>
