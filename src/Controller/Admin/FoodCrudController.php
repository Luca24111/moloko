<?php

namespace App\Controller\Admin;

use App\Entity\Food;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

#[AdminRoute(path: '/ingredient-category', name: 'ingredient_category')]
final class FoodCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Food::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Categorie ingredienti')
            ->setEntityLabelInSingular('Categoria ingredienti')
            ->setDefaultSort(['displayOrder' => 'ASC', 'name' => 'ASC'])
            ->setSearchFields(['name', 'description', 'foodCategory.name', 'foodCategories.name']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nome')->setRequired(false);
        yield TextareaField::new('description', 'Descrizione')
            ->setRequired(false)
            ->hideOnIndex();
        yield AssociationField::new('foodCategories', 'Categoria cibo')
            ->setRequired(false)
            ->setFormTypeOption('by_reference', false)
            ->autocomplete();
        yield IntegerField::new('displayOrder', 'Ordine')->setRequired(false);
        yield BooleanField::new('isEnabled', 'Attiva');
        yield DateTimeField::new('createdAt', 'Creata il')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Aggiornata il')->hideOnForm();
    }
}
