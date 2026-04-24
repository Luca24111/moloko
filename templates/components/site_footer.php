<?php
$footerLinks = $footerLinks ?? [];
$year = $year ?? (int) date('Y');
?>
<footer class="site-footer">
    <div class="site-footer__brand">
        <a href="/" class="brand-mark brand-mark--footer">
            <img
                class="brand-mark__logo"
                src="<?= $e($asset('images/home/logo/logo_moloch.png')); ?>"
                alt=""
                aria-hidden="true"
            >
            <span class="brand-mark__text">molo<span class="brand-mark__k">ch</span></span>
        </a>
        <p>
            Cocktail bar contemporaneo con drink stagionali,
            pairing dedicati e un servizio orientato all esperienza.
        </p>
    </div>

    <div class="site-footer__links">
        <h3>Menu veloce</h3>
        <ul>
            <?php foreach ($footerLinks as $link): ?>
                <li><a href="<?= $e($link['url'] ?? '/'); ?>"><?= $e($link['label'] ?? 'Link'); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <p class="site-footer__legal">&copy; <?= $e((string) $year); ?> moloch. All rights reserved.</p>
</footer>
