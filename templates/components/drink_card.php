<?php
$drink = $drink ?? [];
$compact = $compact ?? false;
$className = $compact ? 'drink-card drink-card--compact' : 'drink-card';
$placeholderImage = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
?>
<article class="<?= $e($className); ?>">
    <img
        class="drink-card__image"
        src="<?= $e($placeholderImage); ?>"
        data-lazy-src="<?= $e($drink['image'] ?? ''); ?>"
        alt="<?= $e($drink['name'] ?? 'Cocktail'); ?>"
        loading="lazy"
        decoding="async"
        fetchpriority="low"
    >

    <div class="drink-card__body">
        <div class="drink-card__head">
            <h3><?= $e($drink['name'] ?? 'Drink'); ?></h3>
        </div>

        <p class="drink-card__description"><?= $e($drink['description'] ?? ''); ?></p>
        <p class="drink-card__price">€ <?= $e($drink['price'] ?? '0.00'); ?></p>
    </div>
</article>
