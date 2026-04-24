<?php
$events = $events ?? [];
?>
<div class="event-grid">
    <?php foreach ($events as $event): ?>
        <?php $include('components/event_card.php', ['event' => $event]); ?>
    <?php endforeach; ?>
</div>
