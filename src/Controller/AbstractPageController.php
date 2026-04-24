<?php

namespace App\Controller;

use App\Service\TemplateRenderer;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractPageController
{
    public function __construct(private readonly TemplateRenderer $renderer)
    {
    }

    protected function renderPage(string $contentTemplate, array $options = []): Response
    {
        $styles = array_values(array_unique(array_merge(
            [
                'css/components/site-header.css',
                'css/components/site-footer.css',
            ],
            $options['styles'] ?? []
        )));

        $payload = [
            'title' => $options['title'] ?? 'moloch Bar',
            'description' => $options['description'] ?? 'Cocktail menu stagionale e atmosfera contemporanea.',
            'styles' => $styles,
            'scripts' => $options['scripts'] ?? [],
            'bodyClass' => $options['bodyClass'] ?? '',
            'contentTemplate' => $contentTemplate,
            'contentData' => $options['content'] ?? [],
            'navItems' => $this->navItems(),
            'currentRoute' => $options['currentRoute'] ?? '',
            'footerLinks' => $this->footerLinks(),
            'year' => (int) date('Y'),
        ];

        return new Response($this->renderer->render('layouts/main.php', $payload));
    }

    private function navItems(): array
    {
        return [
            ['label' => 'Home', 'url' => '/', 'route' => 'home'],
            ['label' => 'Drink', 'url' => '/menu', 'route' => 'menu'],
            ['label' => 'Menu', 'url' => '/food', 'route' => 'food'],
        ];
    }

    private function footerLinks(): array
    {
        return [
            ['label' => 'Drink', 'url' => '/menu'],
            ['label' => 'Menu', 'url' => '/food'],
            ['label' => 'Home', 'url' => '/'],
        ];
    }
}
