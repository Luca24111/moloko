<?php

namespace App\Command;

use App\Service\ImageOptimizer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:optimize-images',
    description: 'Optimizes public images and creates WebP variants for homepage assets.'
)]
final class OptimizeImagesCommand extends Command
{
    public function __construct(
        private readonly ImageOptimizer $imageOptimizer,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $targets = [
            $this->projectDir.'/public/images/home',
            $this->projectDir.'/public/uploads/media',
        ];

        $files = [];
        foreach ($targets as $directory) {
            if (!is_dir($directory)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (!$file instanceof \SplFileInfo || !$file->isFile()) {
                    continue;
                }

                $extension = strtolower($file->getExtension());
                if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                    continue;
                }

                $files[] = $file->getPathname();
            }
        }

        if ($files === []) {
            $io->warning('Nessuna immagine locale trovata nei percorsi pubblici configurati.');

            return Command::SUCCESS;
        }

        $optimizedCount = 0;
        $webpCount = 0;
        $savedBytes = 0;

        foreach ($files as $path) {
            $isHomeAsset = str_contains($path, '/public/images/home/');
            $result = $this->imageOptimizer->optimizeFile($path, $isHomeAsset);

            if ($result['optimized']) {
                ++$optimizedCount;
                $savedBytes += max(0, $result['before'] - $result['after']);
            }

            if ($result['webp_created']) {
                ++$webpCount;
            }
        }

        $io->success(sprintf(
            'Immagini ottimizzate: %d. Varianti WebP create: %d. Spazio recuperato: %.2f MB.',
            $optimizedCount,
            $webpCount,
            $savedBytes / 1024 / 1024
        ));

        return Command::SUCCESS;
    }
}
