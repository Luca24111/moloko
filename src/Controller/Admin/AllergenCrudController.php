<?php

namespace App\Controller\Admin;

use App\Entity\Allergen;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

#[AdminRoute(path: '/allergen', name: 'allergen')]
final class AllergenCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Allergen::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Allergeni')
            ->setEntityLabelInSingular('Allergene')
            ->setDefaultSort(['name' => 'ASC'])
            ->setSearchFields(['name', 'description']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nome');
        yield TextareaField::new('description', 'Descrizione')
            ->setRequired(false)
            ->hideOnIndex();
        yield DateTimeField::new('createdAt', 'Creato il')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Aggiornato il')->hideOnForm();
    }
}
