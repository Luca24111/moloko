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
        decoding="async"
    >

    <div class="drink-card__body">
        <div class="drink-card__head">
            <h3><?= $e($drink['name'] ?? 'Drink'); ?></h3>
        </div>

        <p class="drink-card__description"><?= $e($drink['description'] ?? 'Una proposta firmata Moloch.'); ?></p>
        <p class="drink-card__price">€ <?= $e($drink['price'] ?? '0.00'); ?></p>
    </div>
</article>
