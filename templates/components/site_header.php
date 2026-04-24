<?php
$navItems = $navItems ?? [];
$currentRoute = $currentRoute ?? '';
?>
<header class="site-header">
    <a href="/" class="brand-mark" aria-label="moloch home">
        <img
            class="brand-mark__logo"
            src="<?= $e($asset('images/home/logo/logo_moloch.png')); ?>"
            alt=""
            aria-hidden="true"
        >
        <span class="brand-mark__text">molo<span class="brand-mark__k">ch</span></span>
    </a>

    <nav class="main-nav" aria-label="Navigazione principale">
        <?php foreach ($navItems as $item): ?>
            <?php $isActive = $currentRoute === ($item['route'] ?? ''); ?>
            <a
                class="main-nav__link<?= $isActive ? ' is-active' : ''; ?>"
                href="<?= $e($item['url'] ?? '/'); ?>"
            >
                <?= $e($item['label'] ?? 'Link'); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    
</header>
