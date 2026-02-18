<?php
$categories = $categories ?? [];
$activeCategory = $activeCategory ?? '';
$groupedFoods = $groupedFoods ?? [];
?>
<section class="food-page" data-menu-filter>
    <header class="food-page__header">
        <p class="food-page__kicker">Menu Cucina</p>
        <h1>I nostri piatti</h1>
        <p>
            Scopri i piatti del locale e filtra rapidamente per categoria.
        </p>
    </header>

    <div class="food-page__layout">
        <aside class="food-page__sidebar">
            <h2>Categorie</h2>
            <?php $include('components/category_tabs.php', [
                'categories' => $categories,
                'activeCategory' => $activeCategory,
                'variant' => 'stacked',
                'interactive' => true,
                'includeAll' => true,
                'ariaLabel' => 'Categorie cibo',
            ]); ?>
        </aside>

        <div class="food-page__sections">
            <?php if (empty($groupedFoods)): ?>
                <section class="food-page__section">
                    <p class="food-page__empty">Nessuna categoria cibo disponibile. Aggiungila dal backend.</p>
                </section>
            <?php endif; ?>

            <?php foreach ($groupedFoods as $section): ?>
                <section
                    class="food-page__section"
                    id="<?= $e($section['category']['slug'] ?? 'section'); ?>"
                    data-category-section="<?= $e($section['category']['slug'] ?? ''); ?>"
                >
                    <div class="food-page__section-head">
                        <h3><?= $e($section['category']['label'] ?? 'Categoria'); ?></h3>
                        <p><?= $e($section['category']['description'] ?? 'Descrizione categoria'); ?></p>
                    </div>

                    <?php if (empty($section['foods'])): ?>
                        <p class="food-page__empty">Nessun piatto associato a questa categoria.</p>
                    <?php else: ?>
                        <?php $include('components/drink_grid.php', ['drinks' => $section['foods'] ?? []]); ?>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</section>
