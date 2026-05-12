<?php
$kicker = $kicker ?? 'moloch';
$title = $title ?? '';
$text = $text ?? '';
$primaryCta = $primaryCta ?? ['label' => 'Scopri', 'url' => '/menu'];
$secondaryCta = $secondaryCta ?? null;
$slides = $slides ?? [];
$badge = $badge ?? null;
$visualOnly = (bool) ($visualOnly ?? false);
$placeholderImage = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
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
                    <?php $slideUrl = $slide['url'] ?? ''; ?>
                    <img
                        src="<?= $e($isActive ? $slideUrl : $placeholderImage); ?>"
                        <?php if (!$isActive): ?>data-src="<?= $e($slideUrl); ?>" loading="lazy"<?php else: ?>fetchpriority="high"<?php endif; ?>
                        alt="<?= $e($slide['alt'] ?? 'Slide principale'); ?>"
                        decoding="async"
                    >
                    <?php $slideTitle = trim((string) ($slide['title'] ?? '')); ?>
                    <?php $slideTime = trim((string) ($slide['time'] ?? '')); ?>
                    <?php $slideMeta = trim((string) ($slide['meta'] ?? ('€ '.($slide['price'] ?? '0.00')))); ?>
                    <?php if ($slideTitle !== '' || $slideTime !== '' || $slideMeta !== ''): ?>
                        <figcaption class="hero__caption">
                            <?php if ($slideTitle !== '' || $slideTime !== ''): ?>
                                <div class="hero__event-copy">
                                    <?php if ($slideTitle !== ''): ?>
                                        <strong class="hero__event-title"><?= $e($slideTitle); ?></strong>
                                    <?php endif; ?>
                                </div>
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
