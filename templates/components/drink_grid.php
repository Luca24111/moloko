<?php
$drinks = $drinks ?? [];
$showImages = $showImages ?? true;
$layout = $layout ?? 'cards';
$hasMissingImages = false;
$hasVisibleImages = false;

foreach ($drinks as $drink) {
    $hasImage = (bool) ($drink['has_image'] ?? false);

    if ($hasImage) {
        $hasVisibleImages = true;
        continue;
    }

    $hasMissingImages = true;
}

$resolvedLayout = $layout;
if ($layout !== 'list' && (!$showImages || !$hasVisibleImages)) {
    $resolvedLayout = 'list';
}

$classNames = ['drink-grid'];
if ($resolvedLayout === 'list') {
    $classNames[] = 'drink-grid--list';
} elseif ($hasMissingImages) {
    $classNames[] = 'drink-grid--mixed';
}

$className = implode(' ', $classNames);
?>
<div class="<?= $e($className); ?>">
    <?php foreach ($drinks as $drink): ?>
        <?php $hasImage = $showImages && (bool) ($drink['has_image'] ?? false); ?>
        <?php $include('components/drink_card.php', [
            'drink' => $drink,
            'showImage' => $hasImage,
            'list' => $resolvedLayout === 'list' || !$hasImage,
        ]); ?>
    <?php endforeach; ?>
</div>
