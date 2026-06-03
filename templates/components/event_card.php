<?php
$event = $event ?? [];
$placeholderImage = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
?>
<article class="event-card">
    <img
        class="event-card__image"
        src="<?= $e($placeholderImage); ?>"
        data-lazy-src="<?= $e($event['image'] ?? ''); ?>"
        alt="<?= $e($event['title'] ?? 'Evento'); ?>"
        loading="lazy"
        decoding="async"
        fetchpriority="low"
    >

    <div class="event-card__body">
        <div class="event-card__head">
            <div class="event-card__intro">
                <p class="event-card__date"><?= $e($event['starts_at_label'] ?? 'Data da definire'); ?></p>
                <h3><?= $e($event['title'] ?? 'Evento'); ?></h3>
            </div>
            <span class="event-card__price"><?= $e($event['ticket_label'] ?? 'Ingresso libero'); ?></span>
        </div>

        <p class="event-card__description"><?= $e($event['description'] ?? 'Musica, drink e atmosfera in riva al Po.'); ?></p>

        <div class="event-card__meta">
            <span class="event-card__tag"><?= $e($event['location'] ?? 'Moloch, Borgoforte'); ?></span>
            <span class="event-card__time"><?= $e($event['date_range_label'] ?? 'Orario in arrivo'); ?></span>
        </div>
    </div>
</article>
