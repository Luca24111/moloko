<?php
$hero = $hero ?? [];
$stats = $stats ?? [];
$quickLinks = $quickLinks ?? [];
?>
<section class="home-page">
    <?php $include('components/hero.php', $hero); ?>

    <?php $include('components/feature_banner.php', ['items' => $stats]); ?>

    <section class="home-page__quicklinks">
        <div class="home-page__heading">
            <h2>Accesso rapido</h2>
            <p>Tre scorciatoie utili per arrivare subito dove serve.</p>
        </div>

        <?php if (empty($quickLinks)): ?>
            <p class="home-page__empty">Nessun collegamento disponibile al momento.</p>
        <?php else: ?>
            <div class="home-page__link-grid">
                <?php foreach ($quickLinks as $item): ?>
                    <a class="home-page__link-card" href="<?= $e($item['url'] ?? '/'); ?>">
                        <img
                            class="home-page__link-image"
                            src="<?= $e($item['image'] ?? ''); ?>"
                            alt="<?= $e($item['alt'] ?? ($item['eyebrow'] ?? 'Link')); ?>"
                            loading="lazy"
                        >
                        <span class="home-page__link-eyebrow"><?= $e($item['eyebrow'] ?? 'Link'); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</section>
