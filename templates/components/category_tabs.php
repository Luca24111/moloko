<?php
$categories = $categories ?? [];
$activeCategory = $activeCategory ?? '';
$variant = $variant ?? 'inline';
$interactive = $interactive ?? false;
$includeAll = $includeAll ?? false;
$ariaLabel = $ariaLabel ?? 'Categorie';
$className = $variant === 'stacked' ? 'category-tabs category-tabs--stacked' : 'category-tabs';
?>
<div class="<?= $e($className); ?>" aria-label="<?= $e($ariaLabel); ?>">
    <?php if ($interactive && $includeAll): ?>
        <?php $allActive = $activeCategory === 'all'; ?>
        <button
            type="button"
            class="category-tabs__chip<?= $allActive ? ' is-active' : ''; ?>"
            data-category-filter="all"
            aria-pressed="<?= $allActive ? 'true' : 'false'; ?>"
        >
            Tutte
        </button>
    <?php endif; ?>

    <?php foreach ($categories as $category): ?>
        <?php $slug = $category['slug'] ?? ''; ?>
        <?php $isActive = $activeCategory === $slug; ?>

        <?php if ($interactive): ?>
            <button
                type="button"
                class="category-tabs__chip<?= $isActive ? ' is-active' : ''; ?>"
                data-category-filter="<?= $e($slug); ?>"
                aria-pressed="<?= $isActive ? 'true' : 'false'; ?>"
            >
                <?= $e($category['label'] ?? 'Categoria'); ?>
            </button>
        <?php else: ?>
            <span class="category-tabs__chip<?= $isActive ? ' is-active' : ''; ?>">
                <?= $e($category['label'] ?? 'Categoria'); ?>
            </span>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
