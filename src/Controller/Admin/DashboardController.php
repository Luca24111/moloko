<?php

namespace App\Controller\Admin;

use App\Entity\Allergen;
use App\Entity\Drink;
use App\Entity\DrinkCategory;
use App\Entity\Event;
use App\Entity\Food;
use App\Entity\FoodCategory;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DashboardController extends AbstractDashboardController
{
    public function __construct(private readonly AdminUrlGenerator $adminUrlGenerator)
    {
    }

    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
            ->setController(AlcoholicDrinkCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('moloch Admin')
            ->setFaviconPath('favicon.ico')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-house');

        yield MenuItem::section('Gestione locale');
        yield MenuItem::linkToCrud('Utenti', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Categorie drink', 'fa fa-tags', DrinkCategory::class)
            ->setController(DrinkCategoryCrudController::class);
        yield MenuItem::linkToCrud('Drink alcolici', 'fa fa-martini-glass-citrus', Drink::class)
            ->setController(AlcoholicDrinkCrudController::class);
        yield MenuItem::linkToCrud('Drink analcolici', 'fa fa-glass-water', Drink::class)
            ->setController(NonAlcoholicDrinkCrudController::class);
        yield MenuItem::linkToCrud('Categorie cibo', 'fa fa-layer-group', FoodCategory::class)
            ->setController(FoodCategoryCrudController::class);
        yield MenuItem::linkToCrud('Allergeni', 'fa fa-triangle-exclamation', Allergen::class)
            ->setController(AllergenCrudController::class);
        yield MenuItem::linkToCrud('Piatti', 'fa fa-burger', Food::class)
            ->setController(FoodCrudController::class);
        yield MenuItem::linkToCrud('Eventi', 'fa fa-calendar-day', Event::class);

        yield MenuItem::section('Navigazione');
        yield MenuItem::linkToRoute('Torna al sito', 'fa fa-arrow-left', 'home');
        yield MenuItem::linkToLogout('Logout', 'fa fa-right-from-bracket');
    }
}
