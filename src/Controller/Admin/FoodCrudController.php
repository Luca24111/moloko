<?php

namespace App\Controller\Admin;

use App\Entity\Food;
use App\Service\ManagedMediaStorage;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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
        yield TextField::new('name', 'Nome')->setRequired(false);
        yield TextareaField::new('description', 'Descrizione')
            ->setRequired(false)
            ->hideOnIndex();
        yield AssociationField::new('foodCategory', 'Categoria cibo')
            ->setRequired(false);
        yield MoneyField::new('price', 'Prezzo')
            ->setCurrency('EUR')
            ->setStoredAsCents(false)
            ->setNumDecimals(2)
            ->setRequired(false);
        yield ImageField::new('imageUrl', 'Foto')
            ->setBasePath((string) ManagedMediaStorage::basePathFor(Food::class))
            ->onlyOnIndex();
        yield ImageField::new('imageUrl', 'Foto')
            ->setBasePath((string) ManagedMediaStorage::basePathFor(Food::class))
            ->setUploadDir((string) ManagedMediaStorage::uploadDirFor(Food::class))
            ->setUploadedFileNamePattern(ManagedMediaStorage::UPLOAD_FILENAME_PATTERN)
            ->setRequired(false)
            ->setHelp('Carica un file dal dispositivo. Il file verra gestito e pulito automaticamente dal sistema.')
            ->hideOnIndex();
        yield TextField::new('imageUrl', 'File salvato')
            ->setDisabled()
            ->setHelp('Percorso relativo salvato nel database.')
            ->hideOnIndex();
        yield BooleanField::new('isSpecial', 'Speciale');
        yield BooleanField::new('isEnabled', 'Abilitato');
        yield DateTimeField::new('createdAt', 'Creata il')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Aggiornata il')->hideOnForm();
    }
}
