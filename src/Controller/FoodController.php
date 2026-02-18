<?php

namespace App\Controller;

use App\Service\FrontendMenuProvider;
use App\Service\TemplateRenderer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FoodController extends AbstractPageController
{
    public function __construct(
        TemplateRenderer $renderer,
        private readonly FrontendMenuProvider $menuProvider
    ) {
        parent::__construct($renderer);
    }

    #[Route('/food', name: 'food', methods: ['GET'])]
    public function index(): Response
    {
        $categories = $this->menuProvider->getFoodCategories();
        $groupedFoods = $this->menuProvider->getGroupedFoods();

        return $this->renderPage('pages/food.php', [
            'title' => 'Menu Piatti | moloKo',
            'description' => 'Scopri tutti i piatti del locale, inclusi i fuori menu speciali.',
            'currentRoute' => 'food',
            'styles' => [
                'css/components/category-tabs.css',
                'css/components/drink-card.css',
                'css/pages/food.css',
            ],
            'scripts' => [
                'js/menu-filter.js',
            ],
            'content' => [
                'categories' => $categories,
                'activeCategory' => 'all',
                'groupedFoods' => $groupedFoods,
            ],
        ]);
    }
}
