<?php

namespace App\Controller;

use App\Service\FrontendMenuProvider;
use App\Service\TemplateRenderer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractPageController
{
    public function __construct(
        TemplateRenderer $renderer,
        private readonly FrontendMenuProvider $menuProvider
    ) {
        parent::__construct($renderer);
    }

    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(): Response
    {
        $specialDrinks = $this->menuProvider->getSpecialDrinks(3);
        $heroSlides = array_map(static fn (array $drink): array => [
            'url' => $drink['image'] ?? '',
            'alt' => $drink['name'] ?? 'Drink speciale',
            'title' => $drink['name'] ?? 'Drink speciale',
            'price' => $drink['price'] ?? '0.00',
        ], $specialDrinks);

        if (empty($heroSlides)) {
            $heroSlides = [[
                'url' => 'https://images.unsplash.com/photo-1514361892635-6f5b4d1cd4be?auto=format&fit=crop&w=1200&q=80',
                'alt' => 'Drink speciale',
                'title' => 'Nessun drink speciale disponibile',
                'price' => '0.00',
            ]];
        }

        return $this->renderPage('pages/home.php', [
            'title' => 'moloKo | Cocktail Bar',
            'description' => 'Menu cocktail con signature drink, spritz e mocktail premium.',
            'currentRoute' => 'home',
            'styles' => [
                'css/components/hero.css',
                'css/components/feature-banner.css',
                'css/components/drink-card.css',
                'css/pages/home.css',
            ],
            'scripts' => [
                'js/specials-carousel.js',
            ],
            'content' => [
                'hero' => [
                    'title' => 'I nostri cocktail più esclusivi',
                    'text' => 'Una selezione esclusiva di drink speciali, disponibile per periodi limitati e aggiornata dal team bar.',
                    'primaryCta' => ['label' => 'Vai ai drink speciali', 'url' => '/menu#drink-speciali'],
                    'secondaryCta' => ['label' => 'Apri tutti i drink', 'url' => '/menu'],
                    'slides' => $heroSlides,
                ],
                'stats' => [
                    ['label' => 'Cocktail in carta', 'value' => (string) $this->menuProvider->countDrinks()],
                    ['label' => 'Ingredienti freschi', 'value' => '100%'],
                    ['label' => 'Categorie drink', 'value' => (string) $this->menuProvider->countCategories()],
                    ['label' => 'Rating clienti', 'value' => '4.9/5'],
                ],
                'specialDrinks' => $specialDrinks,
            ],
        ]);
    }
}
