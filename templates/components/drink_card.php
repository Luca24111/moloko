<?php
$drink = $drink ?? [];
$compact = $compact ?? false;
$showImage = $showImage ?? true;
$list = $list ?? false;
$classNames = ['drink-card'];

if ($compact) {
    $classNames[] = 'drink-card--compact';
}

if (!$showImage) {
    $classNames[] = 'drink-card--no-image';
}

if ($list) {
    $classNames[] = 'drink-card--list';
}

$className = implode(' ', $classNames);
$placeholderImage = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
$beerPrices = [];

if (($drink['beer_small_price'] ?? null) !== null) {
    $beerPrices[] = ['label' => 'Piccola', 'price' => $drink['beer_small_price']];
}

if (($drink['beer_medium_price'] ?? null) !== null) {
    $beerPrices[] = ['label' => 'Media', 'price' => $drink['beer_medium_price']];
}
?>
<article class="<?= $e($className); ?>">
    <?php if ($showImage): ?>
        <img
            class="drink-card__image"
            src="<?= $e($placeholderImage); ?>"
            data-lazy-src="<?= $e($drink['image'] ?? ''); ?>"
            alt="<?= $e($drink['name'] ?? 'Cocktail'); ?>"
            loading="lazy"
            decoding="async"
            fetchpriority="low"
        >
    <?php endif; ?>

    <div class="drink-card__body">
        <div class="drink-card__head">
            <h3><?= $e($drink['name'] ?? 'Drink'); ?></h3>
        </div>

        <p class="drink-card__description"><?= $e($drink['description'] ?? ''); ?></p>
        <?php if (!empty($beerPrices)): ?>
            <div class="drink-card__prices">
                <?php foreach ($beerPrices as $beerPrice): ?>
                    <span class="drink-card__price-option">
                        <span><?= $e($beerPrice['label']); ?></span>
                        <strong>€ <?= $e($beerPrice['price']); ?></strong>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="drink-card__price">€ <?= $e($drink['price'] ?? '0.00'); ?></p>
        <?php endif; ?>
    </div>
</article>
