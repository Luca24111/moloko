<?php
$footerLinks = $footerLinks ?? [];
$year = $year ?? (int) date('Y');
$placeholderImage = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
?>
<footer class="site-footer">
    <div class="site-footer__brand">
        <a href="/" class="brand-mark brand-mark--footer">
            <picture class="brand-mark__logo" aria-hidden="true" data-lazy-picture>
                <source data-lazy-srcset="<?= $e($asset('images/home/logo/logo_moloch.webp')); ?>" type="image/webp">
                <img
                    class="brand-mark__logo"
                    src="<?= $e($placeholderImage); ?>"
                    data-lazy-src="<?= $e($asset('images/home/logo/logo_moloch.png')); ?>"
                    alt=""
                    aria-hidden="true"
                    loading="lazy"
                    decoding="async"
                    fetchpriority="low"
                >
            </picture>
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
                <span class="site-footer__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false"><path d="M6.62 10.79a15.46 15.46 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1-.24 11.37 11.37 0 0 0 3.57.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.49a1 1 0 0 1 1 1 11.37 11.37 0 0 0 .57 3.57 1 1 0 0 1-.24 1Z" fill="currentColor"/></svg>
                </span>
                <a href="tel:+3903761689042">0376 168 9042</a>
            </li>
            <li>
                <span class="site-footer__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5Zm0 2.2A2.8 2.8 0 0 0 4.2 7v10A2.8 2.8 0 0 0 7 19.8h10a2.8 2.8 0 0 0 2.8-2.8V7A2.8 2.8 0 0 0 17 4.2Zm10.3 1.5a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2.2a2.8 2.8 0 1 0 0 5.6 2.8 2.8 0 0 0 0-5.6Z" fill="currentColor"/></svg>
                </span>
                <a
                    href="https://www.instagram.com/moloch_lidopo/"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Moloch
                </a>
            </li>
            <li>
                <span class="site-footer__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false"><path d="M12 22s7-6.17 7-12a7 7 0 1 0-14 0c0 5.83 7 12 7 12Zm0-9a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z" fill="currentColor"/></svg>
                </span>
                <span>Via Al Ponte 1, Borgoforte 46030</span>
            </li>
            <li>
                <span class="site-footer__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm1 5v4.38l3.2 1.92-1 1.7L11 12.5V7Z" fill="currentColor"/></svg>
                </span>
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
