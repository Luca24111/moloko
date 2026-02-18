<?php
$categories = $categories ?? [];
$activeCategory = $activeCategory ?? '';
$specialDrinks = $specialDrinks ?? [];
$groupedDrinks = $groupedDrinks ?? [];
?>
<section class="menu-page" data-menu-filter>
    <header class="menu-page__header">
        <p class="menu-page__kicker">Carta Drink</p>
        <h1>Il menu completo del bar</h1>
        <p>
            Esplora la nostra selezione di cocktail. Scegli il tuo preferito o lasciati ispirare dalle nostre proposte esclusive.
        </p>
    </header>

    

    <div class="menu-page__layout">
        <aside class="menu-page__sidebar">
            <h2>Categorie</h2>
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
                    <p class="menu-page__empty">Nessuna categoria drink disponibile. Aggiungile dal backend.</p>
                </section>
            <?php endif; ?>

            <?php foreach ($groupedDrinks as $section): ?>
                <section
                    class="menu-page__section"
                    id="<?= $e($section['category']['slug'] ?? 'section'); ?>"
                    data-category-section="<?= $e($section['category']['slug'] ?? ''); ?>"
                >
                    <div class="menu-page__section-head">
                        <h3><?= $e($section['category']['label'] ?? 'Categoria'); ?></h3>
                        <p><?= $e($section['category']['description'] ?? 'Descrizione categoria'); ?></p>
                    </div>

                    <?php if (empty($section['drinks'])): ?>
                        <p class="menu-page__empty">Nessun drink associato a questa categoria.</p>
                    <?php else: ?>
                        <?php $include('components/drink_grid.php', ['drinks' => $section['drinks'] ?? []]); ?>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</section>
