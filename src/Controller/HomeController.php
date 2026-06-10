<?php

namespace App\Controller;

use App\Service\FrontendMenuProvider;
use App\Service\HomeCardImageResolver;
use App\Service\TemplateRenderer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractPageController
{
    private const DEFAULT_EVENT_IMAGE = '/uploads/media/events/gemini-generated-image-hh5zqmhh5zqmhh5z-b3edea54b518bb68a485469c046f62c93eebf9b2.png';

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
        $heroSlides = array_map(static function (array $event): array {
            $title = trim((string) ($event['title'] ?? ''));

            return [
                'url' => $event['image'] ?? '',
                'alt' => $title !== '' ? $title : 'Evento in evidenza',
                'title' => $title,
                'time' => trim((string) ($event['time_label'] ?? '')),
                'meta' => trim((string) ($event['date_range_label'] ?? '')),
            ];
        }, array_slice($events, 0, 3));

        $heroContent = [];
        if (!empty($heroSlides)) {
            $heroContent = [
                'title' => '',
                'text' => '',
                'primaryCta' => null,
                'secondaryCta' => null,
                'slides' => $heroSlides,
                'visualOnly' => true,
            ];
        } else {
            $heroContent = [
                'title' => '',
                'text' => '',
                'primaryCta' => null,
                'secondaryCta' => null,
                'slides' => [[
                    'url' => self::DEFAULT_EVENT_IMAGE,
                    'alt' => 'Evento del locale',
                    'title' => '',
                    'time' => '',
                    'meta' => '',
                ]],
                'visualOnly' => true,
            ];
        }

        return $this->renderPage('pages/home.php', [
            'title' => 'moloch | Disco Bar sul Po',
            'description' => 'Disco bar a Borgoforte con cocktail, cucina ed eventi in riva al Po.',
            'currentRoute' => 'home',
            'styles' => [
                'css/components/hero.css',
                'css/components/event-card.css',
                'css/components/feature-banner.css',
                'css/pages/home.css',
            ],
            'scripts' => [
                'js/specials-carousel.js',
            ],
            'content' => [
                'hero' => $heroContent,
                'stats' => [
                    ['label' => 'Drink in carta', 'value' => (string) $this->menuProvider->countDrinks()],
                    ['label' => 'Sapore fresco', 'value' => '100%'],
                    ['label' => 'Stili da provare', 'value' => (string) $this->menuProvider->countCategories()],
                    ['label' => 'Serate in programma', 'value' => (string) $this->menuProvider->countPublishedEvents()],
                ],
                'events' => $events,
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
                        'eyebrow' => 'Cucina',
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
