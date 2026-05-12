<?php

namespace App\EventSubscriber;

use App\Service\ManagedMediaStorage;
use App\Service\ImageOptimizer;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

final class ManagedMediaCleanupSubscriber implements EventSubscriber
{
    /**
     * @var array<string, array{0: object|string, 1: string}>
     */
    private array $queuedFiles = [];

    /**
     * @var array<string, array{0: object|string, 1: string}>
     */
    private array $queuedOptimizations = [];

    public function __construct(
        private readonly ManagedMediaStorage $mediaStorage,
        private readonly ImageOptimizer $imageOptimizer,
        private readonly LoggerInterface $logger
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::preUpdate,
            Events::postPersist,
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
        if (is_string($newPath) && $newPath !== '') {
            $this->queueOptimization($entity, $newPath);
        }
    }

    public function postPersist(PostPersistEventArgs $args): void
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

        $this->queueOptimization($entity, $path);
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
        if ($this->queuedOptimizations !== []) {
            $queuedOptimizations = $this->queuedOptimizations;
            $this->queuedOptimizations = [];

            foreach ($queuedOptimizations as [$entity, $path]) {
                try {
                    $this->imageOptimizer->optimizeManagedMedia($entity, $path);
                } catch (\Throwable $exception) {
                    $this->logger->warning('Image optimization skipped after upload.', [
                        'entity' => is_object($entity) ? $entity::class : $entity,
                        'path' => $path,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }
        }

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

    private function queueOptimization(object|string $entity, string $path): void
    {
        $key = (is_object($entity) ? $entity::class : $entity).'|'.$path;
        $this->queuedOptimizations[$key] = [$entity, $path];
    }
}
