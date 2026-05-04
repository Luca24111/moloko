<?php
$categories = $categories ?? [];
$activeCategory = $activeCategory ?? '';
$specialDrinks = $specialDrinks ?? [];
$groupedDrinks = $groupedDrinks ?? [];
?>
<section class="menu-page" data-menu-filter>
    <header class="menu-page__header">
        <p class="menu-page__kicker">Carta Drink</p>
        <h1>Cocktail</h1>
        <p>
            Signature, spritz e miscelati pensati per accompagnare aperitivi, musica e serate in riva al fiume.
        </p>
    </header>

    

    <div class="menu-page__layout">
        <aside class="menu-page__sidebar">
            <h2>Scegli il tuo drink</h2>
            <?php $include('components/category_tabs.php', [
                'categories' => $categories,
                'activeCategory' => $activeCategory,
                'variant' => 'stacked',
                'interactive' => true,
                'includeAll' => true,
            ]); ?>
        </aside>

        <div class="menu-page__sections">
            <?php if (empty($groupedDrinks)): ?>
                <section class="menu-page__section">
                    <p class="menu-page__empty">La carta drink della serata sara disponibile a breve.</p>
                </section>
            <?php endif; ?>

            <?php foreach ($groupedDrinks as $section): ?>
                <section
                    class="menu-page__section"
                    id="<?= $e($section['category']['slug'] ?? 'section'); ?>"
                    data-category-section="<?= $e($section['category']['slug'] ?? ''); ?>"
                >
                    <div class="menu-page__section-head">
                        <h3><?= $e($section['category']['label'] ?? 'Drink della casa'); ?></h3>
                        <p><?= $e($section['category']['description'] ?? 'Selezioni pensate per la notte, dal primo brindisi all ultimo giro.'); ?></p>
                    </div>

                    <?php if (empty($section['drinks'])): ?>
                        <p class="menu-page__empty">Questa selezione sara aggiornata per la prossima serata.</p>
                    <?php else: ?>
                        <?php $include('components/drink_grid.php', ['drinks' => $section['drinks'] ?? []]); ?>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</section>
