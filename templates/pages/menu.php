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
                <?php
                $categoryLabel = trim((string) ($section['category']['label'] ?? ''));
                $imageCategoryLabels = ['lista drink', 'aperitivo'];
                $showDrinkImages = in_array(strtolower($categoryLabel), $imageCategoryLabels, true);
                $isBeerCategory = in_array(strtolower($categoryLabel), ['birra', 'birre'], true);
                ?>
                <section
                    class="menu-page__section<?= $showDrinkImages ? '' : ' menu-page__section--list'; ?>"
                    id="<?= $e($section['category']['slug'] ?? 'section'); ?>"
                    data-category-section="<?= $e($section['category']['slug'] ?? ''); ?>"
                >
                    <div class="menu-page__section-head">
                        <h3><?= $e($section['category']['label'] ?? 'Drink della casa'); ?></h3>
                        <p><?= $e($section['category']['description'] ?? 'Selezioni pensate per la notte, dal primo brindisi all ultimo giro.'); ?></p>
                    </div>

                    <?php if (empty($section['drinks'])): ?>
                        <p class="menu-page__empty">Questa selezione sara aggiornata per la prossima serata.</p>
                    <?php elseif ($isBeerCategory): ?>
                        <?php
                        $beerGroups = [
                            'draft' => ['label' => 'Birre alla spina', 'drinks' => []],
                            'bottle' => ['label' => 'Birre in bottiglietta', 'drinks' => []],
                            'other' => ['label' => 'Altre birre', 'drinks' => []],
                        ];

                        foreach ($section['drinks'] as $drink) {
                            $servingType = $drink['beer_serving_type'] ?? 'other';
                            if ($servingType === 'draft') {
                                $groupKey = 'draft';
                            } elseif ($servingType === 'bottle') {
                                $groupKey = 'bottle';
                            } else {
                                $groupKey = 'other';
                            }

                            $beerGroups[$groupKey]['drinks'][] = $drink;
                        }
                        ?>

                        <div class="menu-page__subsections">
                            <?php foreach ($beerGroups as $beerGroup): ?>
                                <?php if (empty($beerGroup['drinks'])): ?>
                                    <?php continue; ?>
                                <?php endif; ?>

                                <div class="menu-page__subsection">
                                    <h4><?= $e($beerGroup['label']); ?></h4>
                                    <?php $include('components/drink_grid.php', [
                                        'drinks' => $beerGroup['drinks'],
                                        'showImages' => false,
                                        'layout' => 'list',
                                    ]); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <?php $include('components/drink_grid.php', [
                            'drinks' => $section['drinks'] ?? [],
                            'showImages' => $showDrinkImages,
                            'layout' => $showDrinkImages ? 'cards' : 'list',
                        ]); ?>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</section>
