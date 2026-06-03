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

        $scripts = array_values(array_unique(array_merge(
            ['js/lazy-media.js'],
            $options['scripts'] ?? []
        )));

        $payload = [
            'title' => $options['title'] ?? 'moloch Bar',
            'description' => $options['description'] ?? 'Cocktail menu stagionale e atmosfera contemporanea.',
            'styles' => $styles,
            'scripts' => $scripts,
            'bodyClass' => $options['bodyClass'] ?? '',
            'contentTemplate' => $contentTemplate,
            'contentData' => $options['content'] ?? [],
            'navItems' => $this->navItems(),
            'currentRoute' => $options['currentRoute'] ?? '',
            'footerLinks' => $this->footerLinks(),
            'year' => (int) date('Y'),
        ];

        $content = $this->renderer->render('layouts/main.php', $payload);
        $response = new Response($content);
        $response->setPublic();
        $response->setMaxAge(300);
        $response->setSharedMaxAge(300);
        $response->setEtag(sha1($content));

        return $response;
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
