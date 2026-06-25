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

// Avatar palette cycles through reviews.
$avatarPalette = [
  ['bg' => '#e8f1fb', 'fg' => '#174f86'],
  ['bg' => '#e6f6f0', 'fg' => '#17644f'],
  ['bg' => '#f4ecfb', 'fg' => '#64418f'],
  ['bg' => '#fff3dc', 'fg' => '#7a4c12'],
  ['bg' => '#eaf4f7', 'fg' => '#27566a'],
  ['bg' => '#f7ece8', 'fg' => '#884532'],
];

$hasCards = count($reviews) > 0;
$sectionId = 'google-reviews-' . substr(md5($lang . '|' . $googleUrl), 0, 8);
$summaryLabel = (string)t('google_reviews.summary_label', 'Resumen de reseñas de Google');
$starsLabel = (string)t('google_reviews.stars_label', '%s de 5 estrellas');
$slideLabel = (string)t('google_reviews.slide_label', 'Grupo de reseñas %s');
?>
<section class="google-reviews-section" aria-labelledby="<?php echo e($sectionId); ?>">
  <div class="container">
    <div class="google-reviews-shell">

      <div class="google-reviews-header">
        <p class="google-reviews-kicker"><?php echo e(t('google_reviews.eyebrow', 'Clientes que ya confían en nosotros')); ?></p>
        <h2 class="google-reviews-title" id="<?php echo e($sectionId); ?>"><?php echo e(t('google_reviews.title', 'Opiniones de nuestros clientes')); ?></h2>
        <p class="google-reviews-lead"><?php echo e(t('google_reviews.lead', 'Valoraciones reales de clientes que han confiado en +QUECLIMA para instalar, reparar o mantener su climatización.')); ?></p>
      </div>

      <div class="google-reviews-layout">

        <!-- Summary sidebar -->
        <div class="google-reviews-summary" role="group" aria-label="<?php echo e($summaryLabel); ?>">
          <div class="google-rating-score" aria-hidden="true"><?php echo e($rating); ?></div>
          <div class="google-stars google-stars--lg" aria-label="<?php echo e(sprintf((string)t('google_reviews.stars_label', '%s de 5 estrellas'), '5')); ?>">★★★★★</div>
          <strong class="google-score-word"><?php echo e(t('google_reviews.excellent', 'Excelente')); ?></strong>
          <p class="google-score-count"><?php echo e(sprintf((string)t('google_reviews.based_on', 'A base de %s reseñas en Google'), number_format($reviewCount, 0, ',', '.'))); ?></p>

          <div class="google-wordmark" aria-hidden="true">
            <span class="g-blue">G</span><span class="g-red">o</span><span class="g-yellow">o</span><span class="g-blue">g</span><span class="g-green">l</span><span class="g-red">e</span>
          </div>
          <p class="google-reviews-verified"><?php echo e(t('google_reviews.verified', 'Reseñas verificadas en Google')); ?></p>

          <a class="google-reviews-cta" href="<?php echo e($googleUrl); ?>" target="_blank" rel="noopener noreferrer">
            <?php echo e(t('google_reviews.link', 'Ver todas las reseñas')); ?>
            <svg class="google-reviews-cta-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M7 17L17 7M17 7H7M17 7v10" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </a>
        </div>

        <!-- Carousel -->
        <?php if ($hasCards): ?>
        <div class="google-reviews-carousel" data-google-reviews-carousel data-visible="3" data-interval="6500">
          <div class="google-reviews-track">
            <?php foreach ($reviews as $idx => $review): ?>
              <?php
                $color = $avatarPalette[$idx % count($avatarPalette)];
                $isLocalGuide = stripos($review['meta'], 'Local Guide') !== false;
                $chips = [];
                if ($isLocalGuide) {
                  $chips[] = 'Local Guide';
                }
              ?>
              <div class="google-review-card" data-review-index="<?php echo e((string)$idx); ?>">
                <div class="google-review-card-top">
                  <div class="google-review-person">
                    <span class="google-review-avatar" aria-hidden="true" style="--av-bg:<?php echo e($color['bg']); ?>;--av-fg:<?php echo e($color['fg']); ?>;"><?php echo e($initialsFor($review['name'])); ?></span>
                    <div class="google-review-meta">
                      <strong class="google-review-name"><?php echo e($review['name']); ?></strong>
                      <?php if ($review['date'] !== ''): ?>
                        <span class="google-review-date"><?php echo e($review['date']); ?></span>
                      <?php endif; ?>
                    </div>
                  </div>
                  <?php if (!empty($chips)): ?>
                    <div class="google-review-chips">
                      <?php foreach ($chips as $chip): ?>
                        <span class="google-review-chip"><?php echo e($chip); ?></span>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="google-stars google-stars--sm" aria-label="<?php echo e(sprintf($starsLabel, (string)$review['stars'])); ?>"><?php echo e(str_repeat('★', $review['stars']) . str_repeat('☆', 5 - $review['stars'])); ?></div>
                <p class="google-review-text"><?php echo e($review['text']); ?></p>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="google-review-dots" data-dot-label="<?php echo e($slideLabel); ?>"></div>
        </div>
        <?php endif; ?>

      </div><!-- /.google-reviews-layout -->
    </div><!-- /.google-reviews-shell -->
  </div>
</section>
