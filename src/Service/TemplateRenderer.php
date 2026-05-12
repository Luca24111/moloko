<?php

namespace App\Service;

use RuntimeException;
use Symfony\Component\HttpKernel\KernelInterface;

final class TemplateRenderer
{
    public function __construct(private readonly KernelInterface $kernel)
    {
    }

    public function render(string $template, array $context = []): string
    {
        $templatePath = $this->resolveTemplatePath($template);
        $publicDir = rtrim($this->kernel->getProjectDir(), '/').'/public';

        $escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $asset = static function (string $path) use ($publicDir): string {
            $normalizedPath = '/'.ltrim($path, '/');
            $absolutePath = $publicDir.$normalizedPath;

            if (!is_file($absolutePath)) {
                return $normalizedPath;
            }

            return sprintf('%s?v=%s', $normalizedPath, dechex((int) filemtime($absolutePath)));
        };

        $include = function (string $partial, array $locals = []) use (&$include, $escape, $asset): void {
            $partialPath = $this->resolveTemplatePath($partial);

            if (!is_file($partialPath)) {
                throw new RuntimeException(sprintf('Template "%s" non trovato.', $partial));
            }

            extract($locals, EXTR_SKIP);
            $e = $escape;
            require $partialPath;
        };

        extract($context, EXTR_SKIP);
        $e = $escape;

        ob_start();
        require $templatePath;

        return (string) ob_get_clean();
    }

    private function resolveTemplatePath(string $template): string
    {
        $path = rtrim($this->kernel->getProjectDir(), '/').'/templates/'.ltrim($template, '/');

        if (!is_file($path)) {
            throw new RuntimeException(sprintf('Template "%s" non trovato.', $template));
        }

        return $path;
    }
}
