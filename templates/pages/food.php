<?php
$categories = $categories ?? [];
$activeCategory = $activeCategory ?? '';
$groupedFoods = $groupedFoods ?? [];
?>
<section class="food-page" data-menu-filter>
    <header class="food-page__header">
        <p class="food-page__kicker">Cucina</p>
        <h1>Sfiziosità</h1>

    </header>

    <div class="food-page__layout">
        <aside class="food-page__sidebar">
            <h2>Scegli cosa assaggiare</h2>
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
                    <p class="food-page__empty">Le proposte cucina saranno disponibili a breve.</p>
                </section>
            <?php endif; ?>

            <?php foreach ($groupedFoods as $section): ?>
                <?php $foods = $section['foods'] ?? []; ?>
                <section
                    class="food-page__section food-page__section--list"
                    id="<?= $e($section['category']['slug'] ?? 'section'); ?>"
                    data-category-section="<?= $e($section['category']['slug'] ?? ''); ?>"
                >
                    <div class="food-page__section-head">
                        <h3><?= $e($section['category']['label'] ?? 'Proposte cucina'); ?></h3>
                        <p><?= $e($section['category']['description'] ?? 'Sapori semplici e curati da portare al tavolo con un drink.'); ?></p>
                    </div>

                    <?php if (empty($section['foods'])): ?>
                        <p class="food-page__empty">Questa proposta tornera presto in carta.</p>
                    <?php else: ?>
                        <?php $include('components/drink_grid.php', [
                            'drinks' => $foods,
                            'showImages' => false,
                            'layout' => 'list',
                        ]); ?>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</section>
