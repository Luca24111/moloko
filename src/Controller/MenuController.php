<?php

namespace App\Controller;

use App\Service\FrontendMenuProvider;
use App\Service\TemplateRenderer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MenuController extends AbstractPageController
{
    public function __construct(
        TemplateRenderer $renderer,
        private readonly FrontendMenuProvider $menuProvider
    ) {
        parent::__construct($renderer);
    }

    #[Route('/menu', name: 'menu', methods: ['GET'])]
    public function index(): Response
    {
        $categories = $this->menuProvider->getCategories();
        $grouped = $this->menuProvider->getGroupedDrinks();
        $specialDrinks = $this->menuProvider->getSpecialDrinks();

        return $this->renderPage('pages/menu.php', [
            'title' => 'Menu Drink | moloch',
            'description' => 'Cocktail, spritz e signature drink per le serate in riva al Po.',
            'currentRoute' => 'menu',
            'styles' => [
                'css/components/category-tabs.css',
                'css/components/drink-card.css',
                'css/pages/menu.css',
            ],
            'scripts' => [
                'js/menu-filter.js',
            ],
            'content' => [
                'categories' => $categories,
                'activeCategory' => 'all',
                'specialDrinks' => $specialDrinks,
                'groupedDrinks' => $grouped,
            ],
        ]);
    }
}
