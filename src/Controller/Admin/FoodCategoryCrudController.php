<?php

namespace App\Controller\Admin;

use App\Entity\FoodCategory;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

#[AdminRoute(path: '/food-category', name: 'food_category')]
final class FoodCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FoodCategory::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Categorie cibo')
            ->setEntityLabelInSingular('Categoria cibo')
            ->setDefaultSort(['displayOrder' => 'ASC', 'name' => 'ASC'])
            ->setSearchFields(['name', 'description']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nome categoria');
        yield TextareaField::new('description', 'Descrizione')->hideOnIndex();
        yield IntegerField::new('displayOrder', 'Ordine');
        yield BooleanField::new('isActive', 'Attiva');
        yield DateTimeField::new('createdAt', 'Creata il')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Aggiornata il')->hideOnForm();
    }
}

