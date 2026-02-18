<?php
$drink = $drink ?? [];
$compact = $compact ?? false;
$className = $compact ? 'drink-card drink-card--compact' : 'drink-card';
?>
<article class="<?= $e($className); ?>">
    <img
        class="drink-card__image"
        src="<?= $e($drink['image'] ?? ''); ?>"
        alt="<?= $e($drink['name'] ?? 'Cocktail'); ?>"
        loading="lazy"
    >

    <div class="drink-card__body">
        <div class="drink-card__head">
            <h3><?= $e($drink['name'] ?? 'Drink'); ?></h3>
            <span>EUR <?= $e($drink['price'] ?? '0.00'); ?></span>
        </div>

        <p class="drink-card__description"><?= $e($drink['description'] ?? 'Descrizione drink.'); ?></p>

        <div class="drink-card__meta">
            <span class="drink-card__tag"><?= $e($drink['category'] ?? 'signature'); ?></span>
        </div>
    </div>
</article>
