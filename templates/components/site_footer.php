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
            Disco bar in riva al fiume Po con cocktail, musica e serate
            dedicate agli eventi dal tramonto fino a tarda notte.
        </p>
    </div>

    <div class="site-footer__contact">
        <h3>Dove trovarci</h3>
        <ul>
            <li>
                <i class="fa-solid fa-phone" aria-hidden="true"></i>
                <a href="tel:+3903761689042">0376 168 9042</a>
            </li>
            <li>
                <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                <a
                    href="https://www.instagram.com/moloch_lidopo/"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Moloch
                </a>
            </li>
            <li>
                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                <span>Via Al Ponte 1, Borgoforte 46030</span>
            </li>
            <li>
                <i class="fa-solid fa-clock" aria-hidden="true"></i>
                <span>Aperto da martedi a domenica, 17:00 - 2:00 AM</span>
            </li>
        </ul>
    </div>

    <div class="site-footer__links">
        <h3>Scopri Moloch</h3>
        <ul>
            <?php foreach ($footerLinks as $link): ?>
                <li><a href="<?= $e($link['url'] ?? '/'); ?>"><?= $e($link['label'] ?? 'Link'); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <p class="site-footer__legal">&copy; <?= $e((string) $year); ?> moloch. All rights reserved.</p>
</footer>
