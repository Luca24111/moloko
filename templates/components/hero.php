<?php
$kicker = $kicker ?? 'moloch';
$title = $title ?? '';
$text = $text ?? '';
$primaryCta = $primaryCta ?? ['label' => 'Scopri', 'url' => '/menu'];
$secondaryCta = $secondaryCta ?? null;
$slides = $slides ?? [];
$badge = $badge ?? null;
$visualOnly = (bool) ($visualOnly ?? false);
?>
<section class="hero<?= $visualOnly ? ' hero--visual-only' : ''; ?>">
    <?php if ($title !== ''): ?>
        <div class="hero__copy-top">
            <h1 class="hero__title"><?= $e($title); ?></h1>
        </div>
    <?php endif; ?>

    <div class="hero__visual" data-special-carousel>
        <div class="hero__slides">
            <?php foreach ($slides as $index => $slide): ?>
                <?php $isActive = $index === 0; ?>
                <figure class="hero__slide<?= $isActive ? ' is-active' : ''; ?>"<?= $isActive ? '' : ' hidden'; ?>>
                    <img src="<?= $e($slide['url'] ?? ''); ?>" alt="<?= $e($slide['alt'] ?? 'Slide principale'); ?>">
                    <?php $slideTitle = trim((string) ($slide['title'] ?? '')); ?>
                    <?php $slideMeta = trim((string) ($slide['meta'] ?? ('€ '.($slide['price'] ?? '0.00')))); ?>
                    <?php if ($slideTitle !== '' || $slideMeta !== ''): ?>
                        <figcaption class="hero__caption">
                            <?php if ($slideTitle !== ''): ?>
                                <strong class="hero__event-title"><?= $e($slideTitle); ?></strong>
                            <?php endif; ?>
                            <?php if ($slideMeta !== ''): ?>
                                <span class="hero__event-date"><?= $e($slideMeta); ?></span>
                            <?php endif; ?>
                        </figcaption>
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>

        <?php if (count($slides) > 1): ?>
            <button type="button" class="hero__control hero__control--prev" data-carousel-prev aria-label="Slide precedente">&larr;</button>
            <button type="button" class="hero__control hero__control--next" data-carousel-next aria-label="Slide successiva">&rarr;</button>

            <div class="hero__dots">
                <?php foreach ($slides as $index => $slide): ?>
                    <button
                        type="button"
                        class="hero__dot<?= $index === 0 ? ' is-active' : ''; ?>"
                        data-carousel-dot="<?= $e((string) $index); ?>"
                        aria-label="Vai alla slide <?= $e((string) ($index + 1)); ?>"
                        aria-pressed="<?= $index === 0 ? 'true' : 'false'; ?>"
                    ></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (is_string($badge) && $badge !== ''): ?>
            <span class="hero__badge"><?= $e($badge); ?></span>
        <?php endif; ?>
    </div>

    <?php if ($text !== '' || is_array($primaryCta) || is_array($secondaryCta)): ?>
        <div class="hero__copy-bottom">
            <?php if ($text !== ''): ?>
                <p class="hero__text"><?= $e($text); ?></p>
            <?php endif; ?>

            <?php if (is_array($primaryCta) || is_array($secondaryCta)): ?>
                <div class="hero__actions">
                    
                    <?php if (is_array($secondaryCta)): ?>
                        <a class="btn btn--ghost" href="<?= $e($secondaryCta['url'] ?? '/menu'); ?>">
                            <?= $e($secondaryCta['label'] ?? 'Menu'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
