<?php
$hero = $hero ?? [];
$stats = $stats ?? [];
$specialDrinks = $specialDrinks ?? [];
?>
<section class="home-page">
    <?php $include('components/hero.php', $hero); ?>

    <?php $include('components/feature_banner.php', ['items' => $stats]); ?>

    <section class="home-page__specials">
        <div class="home-page__heading">
            <h2>Drink speciali in evidenza</h2>
            <p>In home vengono mostrati solo drink speciali, massimo 3.</p>
        </div>

        <?php if (empty($specialDrinks)): ?>
            <p class="home-page__empty">Nessun drink speciale disponibile al momento.</p>
        <?php else: ?>
            <?php $include('components/drink_grid.php', ['drinks' => $specialDrinks]); ?>
        <?php endif; ?>

        <a class="btn btn--solid" href="/menu#drink-speciali">Vai alla sezione drink speciali</a>
    </section>
</section>
