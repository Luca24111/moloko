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
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

#[AdminRoute(path: '/food', name: 'food')]
final class FoodCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Food::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Piatti')
            ->setEntityLabelInSingular('Piatto')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['name', 'description', 'foodCategory.name']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nome');
        yield TextareaField::new('description', 'Descrizione')->hideOnIndex();
        yield AssociationField::new('foodCategory', 'Categoria cibo')
            ->setRequired(false);
        yield MoneyField::new('price', 'Prezzo')
            ->setCurrency('EUR')
            ->setStoredAsCents(false)
            ->setNumDecimals(2);
        yield UrlField::new('imageUrl', 'Foto (URL)')->hideOnIndex();
        yield BooleanField::new('isSpecial', 'Speciale');
        yield BooleanField::new('isEnabled', 'Abilitato');
        yield DateTimeField::new('createdAt', 'Creata il')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Aggiornata il')->hideOnForm();
    }
}
