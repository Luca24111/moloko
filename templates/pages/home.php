<?php
$hero = $hero ?? [];
$stats = $stats ?? [];
$events = $events ?? [];
$quickLinks = $quickLinks ?? [];
$placeholderImage = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
?>
<section class="home-page">
    <?php $include('components/hero.php', $hero); ?>

    <?php $include('components/feature_banner.php', ['items' => $stats]); ?>



    <section class="home-page__quicklinks">
        <div class="home-page__heading">
            <p class="home-page__kicker">Menu</p>
            <h2>Drink e cucina</h2>
        </div>

        <?php if (empty($quickLinks)): ?>
            <p class="home-page__empty">Le proposte della serata saranno disponibili a breve.</p>
        <?php else: ?>
            <div class="home-page__link-grid">
                <?php foreach ($quickLinks as $item): ?>
                    <a class="home-page__link-card" href="<?= $e($item['url'] ?? '/'); ?>">
                        <img
                            class="home-page__link-image"
                            src="<?= $e($placeholderImage); ?>"
                            data-lazy-src="<?= $e($item['image'] ?? ''); ?>"
                            alt="<?= $e($item['alt'] ?? ($item['eyebrow'] ?? 'Link')); ?>"
                            loading="lazy"
                            decoding="async"
                            fetchpriority="low"
                        >
                        <span class="home-page__link-eyebrow"><?= $e($item['eyebrow'] ?? 'Link'); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</section>
