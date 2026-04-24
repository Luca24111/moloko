<?php
$event = $event ?? [];
?>
<article class="event-card">
    <img
        class="event-card__image"
        src="<?= $e($event['image'] ?? ''); ?>"
        alt="<?= $e($event['title'] ?? 'Evento'); ?>"
        loading="lazy"
    >

    <div class="event-card__body">
        <div class="event-card__head">
            <div class="event-card__intro">
                <p class="event-card__date"><?= $e($event['starts_at_label'] ?? 'Data da definire'); ?></p>
                <h3><?= $e($event['title'] ?? 'Evento'); ?></h3>
            </div>
            <span class="event-card__price"><?= $e($event['ticket_label'] ?? 'Ingresso libero'); ?></span>
        </div>

        <p class="event-card__description"><?= $e($event['description'] ?? 'Dettagli evento in aggiornamento.'); ?></p>

        <div class="event-card__meta">
            <span class="event-card__tag"><?= $e($event['location'] ?? 'Location da definire'); ?></span>
            <span class="event-card__time"><?= $e($event['date_range_label'] ?? 'Orario da definire'); ?></span>
        </div>
    </div>
</article>
