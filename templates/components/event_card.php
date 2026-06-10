<?php
$event = $event ?? [];
$placeholderImage = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
$title = trim((string) ($event['title'] ?? ''));
$startsAtLabel = trim((string) ($event['starts_at_label'] ?? ''));
$description = trim((string) ($event['description'] ?? ''));
$location = trim((string) ($event['location'] ?? ''));
$dateRangeLabel = trim((string) ($event['date_range_label'] ?? ''));
?>
<article class="event-card">
    <img
        class="event-card__image"
        src="<?= $e($placeholderImage); ?>"
        data-lazy-src="<?= $e($event['image'] ?? ''); ?>"
        alt="<?= $e($title !== '' ? $title : 'Evento'); ?>"
        loading="lazy"
        decoding="async"
        fetchpriority="low"
    >

    <div class="event-card__body">
        <div class="event-card__head">
            <?php if ($startsAtLabel !== '' || $title !== ''): ?>
                <div class="event-card__intro">
                    <?php if ($startsAtLabel !== ''): ?>
                        <p class="event-card__date"><?= $e($startsAtLabel); ?></p>
                    <?php endif; ?>
                    <?php if ($title !== ''): ?>
                        <h3><?= $e($title); ?></h3>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <span class="event-card__price"><?= $e($event['ticket_label'] ?? 'Ingresso libero'); ?></span>
        </div>

        <?php if ($description !== ''): ?>
            <p class="event-card__description"><?= $e($description); ?></p>
        <?php endif; ?>

        <?php if ($location !== '' || $dateRangeLabel !== ''): ?>
            <div class="event-card__meta">
                <?php if ($location !== ''): ?>
                    <span class="event-card__tag"><?= $e($location); ?></span>
                <?php endif; ?>
                <?php if ($dateRangeLabel !== ''): ?>
                    <span class="event-card__time"><?= $e($dateRangeLabel); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</article>
