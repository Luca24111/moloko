<?php

namespace App\EventSubscriber;

use App\Service\ManagedMediaStorage;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

final class ManagedMediaCleanupSubscriber implements EventSubscriber
{
    /**
     * @var array<string, array{0: object|string, 1: string}>
     */
    private array $queuedFiles = [];

    public function __construct(private readonly ManagedMediaStorage $mediaStorage)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::preUpdate,
            Events::postRemove,
            Events::postFlush,
        ];
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        $property = ManagedMediaStorage::propertyFor($entity);
        if ($property === null || !$args->hasChangedField($property)) {
            return;
        }

        $oldPath = $args->getOldValue($property);
        $newPath = $args->getNewValue($property);

        if (!is_string($oldPath) || $oldPath === '' || $oldPath === $newPath) {
            return;
        }

        $this->queue($entity, $oldPath);
    }

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $entity = $args->getObject();
        $property = ManagedMediaStorage::propertyFor($entity);
        if ($property === null) {
            return;
        }

        $getter = 'get'.ucfirst($property);
        if (!method_exists($entity, $getter)) {
            return;
        }

        $path = $entity->{$getter}();
        if (!is_string($path) || $path === '') {
            return;
        }

        $this->queue($entity, $path);
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if ($this->queuedFiles === []) {
            return;
        }

        $queuedFiles = $this->queuedFiles;
        $this->queuedFiles = [];

        foreach ($queuedFiles as [$entity, $path]) {
            $this->mediaStorage->deleteStoredFile($entity, $path);
        }
    }

    private function queue(object|string $entity, string $path): void
    {
        $key = (is_object($entity) ? $entity::class : $entity).'|'.$path;
        $this->queuedFiles[$key] = [$entity, $path];
    }
}
