<?php
$drinks = $drinks ?? [];
$showImages = $showImages ?? true;
$layout = $layout ?? 'cards';
$className = $layout === 'list' ? 'drink-grid drink-grid--list' : 'drink-grid';
?>
<div class="<?= $e($className); ?>">
    <?php foreach ($drinks as $drink): ?>
        <?php $include('components/drink_card.php', [
            'drink' => $drink,
            'showImage' => $showImages,
            'list' => $layout === 'list',
        ]); ?>
    <?php endforeach; ?>
</div>
