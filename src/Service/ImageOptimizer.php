<?php

namespace App\Service;

use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class ImageOptimizer
{
    private const MAX_WIDTH = 1920;
    private const MAX_HEIGHT = 1920;
    private const JPEG_QUALITY = 82;
    private const WEBP_QUALITY = 80;

    private readonly ImageManager $imageManager;

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir
    ) {
        $this->imageManager = ImageManager::gd([
            'autoOrientation' => true,
            'decodeAnimation' => false,
            'strip' => true,
        ]);
    }

    /**
     * @return array{optimized: bool, before: int, after: int, webp_created: bool, webp_path: ?string}
     */
    public function optimizeFile(string $absolutePath, bool $createWebpVariant = false): array
    {
        if (!is_file($absolutePath) || !is_readable($absolutePath) || !is_writable($absolutePath)) {
            return $this->result(false, 0, 0, false, null);
        }

        $before = (int) filesize($absolutePath);
        if ($before <= 0) {
            return $this->result(false, 0, 0, false, null);
        }

        $extension = strtolower(pathinfo($absolutePath, \PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return $this->result(false, $before, $before, false, null);
        }

        $image = $this->imageManager->read($absolutePath);
        $image->scaleDown(width: self::MAX_WIDTH, height: self::MAX_HEIGHT);

        $encoded = $this->encodeForExtension($image, $extension);
        $encoded->save($absolutePath);

        $webpCreated = false;
        $webpPath = null;

        if ($createWebpVariant && $extension !== 'webp') {
            $webpPath = preg_replace('/\.[^.]+$/', '.webp', $absolutePath);

            if (is_string($webpPath) && $webpPath !== '') {
                $image->toWebp(self::WEBP_QUALITY, strip: true)->save($webpPath);
                $webpCreated = is_file($webpPath);
            }
        }

        clearstatcache(true, $absolutePath);
        if ($webpPath !== null) {
            clearstatcache(true, $webpPath);
        }

        return $this->result(
            true,
            $before,
            (int) filesize($absolutePath),
            $webpCreated,
            $webpCreated ? $webpPath : null
        );
    }

    public function optimizeManagedMedia(object|string $entity, ?string $storedPath): bool
    {
        $absolutePath = $this->absoluteManagedPath($entity, $storedPath);
        if ($absolutePath === null) {
            return false;
        }

        return $this->optimizeFile($absolutePath)['optimized'];
    }

    public function publicPath(string $absolutePath): ?string
    {
        $publicRoot = $this->projectDir.'/public/';
        if (!str_starts_with($absolutePath, $publicRoot)) {
            return null;
        }

        return '/'.ltrim(substr($absolutePath, strlen($publicRoot)), '/');
    }

    private function absoluteManagedPath(object|string $entity, ?string $storedPath): ?string
    {
        $normalizedPath = trim((string) $storedPath);
        if (
            $normalizedPath === ''
            || str_contains($normalizedPath, '..')
            || (bool) preg_match('#^(?:https?:)?//#i', $normalizedPath)
            || str_starts_with($normalizedPath, 'data:')
        ) {
            return null;
        }

        $basePath = ManagedMediaStorage::basePathFor($entity);
        if ($basePath === null) {
            return null;
        }

        return $this->projectDir.'/public/'.ltrim($basePath, '/').'/'.ltrim($normalizedPath, '/');
    }

    private function encodeForExtension(ImageInterface $image, string $extension): EncodedImageInterface
    {
        return match ($extension) {
            'jpg', 'jpeg' => $image->toJpeg(self::JPEG_QUALITY, progressive: true, strip: true),
            'png' => $image->toPng(interlaced: false, indexed: false),
            'webp' => $image->toWebp(self::WEBP_QUALITY, strip: true),
            default => throw new \InvalidArgumentException(sprintf('Unsupported image extension "%s".', $extension)),
        };
    }

    /**
     * @return array{optimized: bool, before: int, after: int, webp_created: bool, webp_path: ?string}
     */
    private function result(bool $optimized, int $before, int $after, bool $webpCreated, ?string $webpPath): array
    {
        return [
            'optimized' => $optimized,
            'before' => $before,
            'after' => $after,
            'webp_created' => $webpCreated,
            'webp_path' => $webpPath,
        ];
    }
}
