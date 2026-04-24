<?php

namespace App\Controller;

use App\Service\FrontendMenuProvider;
use App\Service\HomeCardImageResolver;
use App\Service\TemplateRenderer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractPageController
{
    public function __construct(
        TemplateRenderer $renderer,
        private readonly FrontendMenuProvider $menuProvider,
        private readonly HomeCardImageResolver $homeCardImageResolver
    ) {
        parent::__construct($renderer);
    }

    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(): Response
    {
        $events = $this->menuProvider->getPublishedEvents(4);
        $featuredDrink = $this->menuProvider->getSpecialDrinks(1)[0] ?? null;
        $featuredFood = $this->menuProvider->getFoods()[0] ?? null;
        $heroSlides = array_map(static fn (array $event): array => [
            'url' => $event['image'] ?? '',
            'alt' => $event['title'] ?? 'Evento',
            'title' => $event['title'] ?? 'Evento',
            'meta' => $event['date_range_label'] ?? '',
        ], array_slice($events, 0, 3));

        if (empty($heroSlides)) {
            $heroSlides = [[
                'url' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=1200&q=80',
                'alt' => 'Evento del locale',
                'title' => '',
                'meta' => '',
            ]];
        }

        return $this->renderPage('pages/home.php', [
            'title' => 'moloch | Cocktail Bar',
            'description' => 'Menu cocktail con signature drink, spritz e mocktail premium.',
            'currentRoute' => 'home',
            'styles' => [
                'css/components/hero.css',
                'css/components/feature-banner.css',
                'css/pages/home.css',
            ],
            'scripts' => [
                'js/specials-carousel.js',
            ],
            'content' => [
                'hero' => [
                    'title' => '',
                    'text' => '',
                    'primaryCta' => null,
                    'secondaryCta' => null,
                    'slides' => $heroSlides,
                    'visualOnly' => true,
                ],
                'stats' => [
                    ['label' => 'Cocktail in carta', 'value' => (string) $this->menuProvider->countDrinks()],
                    ['label' => 'Ingredienti freschi', 'value' => '100%'],
                    ['label' => 'Categorie drink', 'value' => (string) $this->menuProvider->countCategories()],
                    ['label' => 'Eventi pubblicati', 'value' => (string) $this->menuProvider->countPublishedEvents()],
                ],
                'quickLinks' => [
                    [
                        'eyebrow' => 'Drink',
                        'image' => $this->homeCardImageResolver->resolve(
                            'drink',
                            $featuredDrink['image'] ?? 'https://images.unsplash.com/photo-1514361892635-6f5b4d1cd4be?auto=format&fit=crop&w=1200&q=80'
                        ),
                        'alt' => $featuredDrink['name'] ?? 'Carta drink',
                        'url' => '/menu',
                    ],
                    [
                        'eyebrow' => 'Menu',
                        'image' => $this->homeCardImageResolver->resolve(
                            'menu',
                            $featuredFood['image'] ?? 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=1200&q=80'
                        ),
                        'alt' => $featuredFood['name'] ?? 'Menu piatti',
                        'url' => '/food',
                    ],
                ],
            ],
        ]);
    }
}
