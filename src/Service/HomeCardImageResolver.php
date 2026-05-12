<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class HomeCardImageResolver
{
    private const PUBLIC_BASE_PATH = '/images/home';

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir
    ) {
    }

    public function resolve(string $slot, string $fallback): string
    {
        $files = $this->filesForSlot($slot);
        if ($files === []) {
            return $fallback;
        }

        return self::PUBLIC_BASE_PATH.'/'.$slot.'/'.basename($files[0]);
    }

    /**
     * @return list<string>
     */
    private function filesForSlot(string $slot): array
    {
        $directory = $this->projectDir.'/public'.self::PUBLIC_BASE_PATH.'/'.$slot;
        if (!is_dir($directory)) {
            return [];
        }

        $files = glob($directory.'/*.{jpg,jpeg,png,webp,avif,gif,JPG,JPEG,PNG,WEBP,AVIF,GIF}', \GLOB_BRACE);
        if ($files === false) {
            return [];
        }

        usort($files, static function (string $left, string $right): int {
            $extensionPriority = [
                'avif' => 0,
                'webp' => 1,
                'jpg' => 2,
                'jpeg' => 3,
                'png' => 4,
                'gif' => 5,
            ];

            $leftExtension = strtolower(pathinfo($left, \PATHINFO_EXTENSION));
            $rightExtension = strtolower(pathinfo($right, \PATHINFO_EXTENSION));
            $leftPriority = $extensionPriority[$leftExtension] ?? 99;
            $rightPriority = $extensionPriority[$rightExtension] ?? 99;

            return [$leftPriority, strtolower($left)] <=> [$rightPriority, strtolower($right)];
        });

        return array_values(array_filter($files, 'is_file'));
    }
}
