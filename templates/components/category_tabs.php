<?php
$categories = $categories ?? [];
$activeCategory = $activeCategory ?? '';
$variant = $variant ?? 'inline';
$interactive = $interactive ?? false;
$includeAll = $includeAll ?? false;
$ariaLabel = $ariaLabel ?? 'Categorie';
$className = $variant === 'stacked' ? 'category-tabs category-tabs--stacked' : 'category-tabs';

$items = [];

if ($interactive && $includeAll) {
    $items[] = [
        'slug' => 'all',
        'label' => 'Tutte',
        'active' => $activeCategory === 'all',
    ];
}

foreach ($categories as $category) {
    $slug = $category['slug'] ?? '';
    $items[] = [
        'slug' => $slug,
        'label' => $category['label'] ?? 'Categoria',
        'active' => $activeCategory === $slug,
    ];
}
?>
<div class="<?= $e($className); ?>" aria-label="<?= $e($ariaLabel); ?>">
    <?php foreach ($items as $item): ?>
        <?php if ($interactive): ?>
            <button
                type="button"
                class="category-tabs__chip<?= $item['active'] ? ' is-active' : ''; ?>"
                data-category-filter="<?= $e($item['slug']); ?>"
                aria-pressed="<?= $item['active'] ? 'true' : 'false'; ?>"
            >
                <?= $e($item['label']); ?>
            </button>
        <?php else: ?>
            <span class="category-tabs__chip<?= $item['active'] ? ' is-active' : ''; ?>">
                <?= $e($item['label']); ?>
            </span>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
