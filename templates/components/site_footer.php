<?php
$footerLinks = $footerLinks ?? [];
$year = $year ?? (int) date('Y');
?>
<footer class="site-footer">
    <div class="site-footer__brand">
        <a href="/" class="brand-mark brand-mark--footer">
            <span class="brand-mark__dot"></span>
            <span class="brand-mark__text">molo<span class="brand-mark__k">K</span>o</span>
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

    <p class="site-footer__legal">&copy; <?= $e((string) $year); ?> moloKo. All rights reserved.</p>
</footer>
