<?php
$drinks = $drinks ?? [];
?>
<div class="drink-grid">
    <?php foreach ($drinks as $drink): ?>
        <?php $include('components/drink_card.php', ['drink' => $drink]); ?>
    <?php endforeach; ?>
</div>
