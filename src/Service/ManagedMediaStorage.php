<?php

namespace App\Service;

use App\Entity\Drink;
use App\Entity\Event;
use App\Entity\Food;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class ManagedMediaStorage
{
    public const PUBLIC_ROOT = '/uploads/media';
    public const UPLOAD_FILENAME_PATTERN = '[slug]-[contenthash].[extension]';

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir
    ) {
    }

    public static function propertyFor(object|string $entity): ?string
    {
        return match (self::entityClass($entity)) {
            Drink::class, Food::class => 'imageUrl',
            Event::class => 'coverImageUrl',
            default => null,
        };
    }

    public static function basePathFor(object|string $entity): ?string
    {
        return match (self::entityClass($entity)) {
            Drink::class => self::PUBLIC_ROOT.'/drinks',
            Food::class => self::PUBLIC_ROOT.'/foods',
            Event::class => self::PUBLIC_ROOT.'/events',
            default => null,
        };
    }

    public static function uploadDirFor(object|string $entity): ?string
    {
        return match (self::entityClass($entity)) {
            Drink::class => 'public/uploads/media/drinks',
            Food::class => 'public/uploads/media/foods',
            Event::class => 'public/uploads/media/events',
            default => null,
        };
    }

    public function resolvePublicUrl(object|string $entity, ?string $storedPath, string $fallback): string
    {
        $path = $this->normalizeForEntity($entity, $storedPath);
        if ($path === '') {
            return $fallback;
        }

        if ($this->isExternalPath($path) || str_starts_with($path, 'data:')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return $path;
        }

        $basePath = self::basePathFor($entity);
        if ($basePath === null) {
            return $fallback;
        }

        $preferredPath = $this->preferredVariantPath($entity, $path);

        return rtrim($basePath, '/').'/'.ltrim($preferredPath ?? $path, '/');
    }

    public function deleteStoredFile(object|string $entity, ?string $storedPath): void
    {
        $absolutePath = $this->absolutePathFor($entity, $storedPath);
        if ($absolutePath === null || !is_file($absolutePath)) {
            return;
        }

        @unlink($absolutePath);
    }

    private function absolutePathFor(object|string $entity, ?string $storedPath): ?string
    {
        $path = $this->normalizeForEntity($entity, $storedPath);
        if ($path === '' || $this->isExternalPath($path) || str_starts_with($path, 'data:')) {
            return null;
        }

        if (str_contains($path, '..')) {
            return null;
        }

        $basePath = self::basePathFor($entity);
        if ($basePath === null) {
            return null;
        }

        $normalizedRoot = ltrim(self::PUBLIC_ROOT, '/').'/';
        $normalizedBasePath = ltrim($basePath, '/').'/';
        $normalizedPath = ltrim($path, '/');

        if (str_starts_with($normalizedPath, $normalizedRoot)) {
            if (!str_starts_with($normalizedPath, $normalizedBasePath)) {
                return null;
            }

            return $this->projectDir.'/public/'.$normalizedPath;
        }

        return $this->projectDir.'/public/'.$normalizedBasePath.ltrim($path, '/');
    }

    private function preferredVariantPath(object|string $entity, string $storedPath): ?string
    {
        $normalizedPath = ltrim($storedPath, '/');
        $extension = strtolower(pathinfo($normalizedPath, \PATHINFO_EXTENSION));
        if ($extension === '' || $extension === 'webp') {
            return null;
        }

        $absolutePath = $this->absolutePathFor($entity, $storedPath);
        if ($absolutePath === null) {
            return null;
        }

        $variantPath = preg_replace('/\.[^.]+$/', '.webp', $absolutePath);
        if (!is_string($variantPath) || !is_file($variantPath)) {
            return null;
        }

        return preg_replace('/\.[^.]+$/', '.webp', $normalizedPath) ?: null;
    }

    private function isExternalPath(string $path): bool
    {
        return (bool) preg_match('#^(?:https?:)?//#i', $path);
    }

    private static function entityClass(object|string $entity): string
    {
        return is_object($entity) ? $entity::class : $entity;
    }

    private function normalizeForEntity(object|string $entity, ?string $storedPath): string
    {
        $path = trim((string) $storedPath);
        if ($path === '' || $this->isExternalPath($path) || str_starts_with($path, 'data:')) {
            return $path;
        }

        $normalized = str_replace('\\', '/', $path);
        $basePath = self::basePathFor($entity);
        if ($basePath === null) {
            return ltrim($normalized, '/');
        }

        $basePrefix = ltrim($basePath, '/').'/';
        $publicBasePrefix = 'public/'.$basePrefix;
        $trimmed = ltrim($normalized, '/');

        if (str_starts_with($trimmed, $publicBasePrefix)) {
            return substr($trimmed, strlen($publicBasePrefix));
        }

        if (str_starts_with($trimmed, $basePrefix)) {
            return substr($trimmed, strlen($basePrefix));
        }

        return $trimmed;
    }
}
