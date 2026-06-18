<?php

namespace App\Controller\Admin;

use App\Entity\Ingredienti;
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

#[AdminRoute(path: '/ingredienti', name: 'ingredienti')]
final class IngredientiCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ingredienti::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Ingredienti')
            ->setEntityLabelInSingular('Ingrediente')
            ->setDefaultSort(['name' => 'ASC'])
            ->setSearchFields(['name', 'description', 'ingredientCategory.name', 'allergens.name']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nome')->setRequired(false);
        yield TextareaField::new('description', 'Descrizione')
            ->setRequired(false)
            ->hideOnIndex();
        yield AssociationField::new('ingredientCategory', 'Categoria ingredienti')
            ->setRequired(false);
        yield AssociationField::new('allergens', 'Allergeni')
            ->setRequired(false)
            ->setFormTypeOption('by_reference', false)
            ->autocomplete()
            ->hideOnIndex();
        yield MoneyField::new('price', 'Prezzo')
            ->setCurrency('EUR')
            ->setStoredAsCents(false)
            ->setNumDecimals(2)
            ->setRequired(false);
        yield BooleanField::new('isEnabled', 'Abilitato');
        yield DateTimeField::new('createdAt', 'Creato il')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Aggiornato il')->hideOnForm();
    }
}
