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
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image as ImageConstraint;

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
            ->setFileConstraints([
                new File(
                    maxSize: '20M',
                    mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                    mimeTypesMessage: 'Carica immagini JPG, PNG o WebP.',
                    maxSizeMessage: 'L immagine supera il limite di 20MB.'
                ),
                new ImageConstraint(
                    maxWidth: 8000,
                    maxHeight: 8000,
                    maxWidthMessage: 'L immagine e troppo larga. Usa massimo 8000px.',
                    maxHeightMessage: 'L immagine e troppo alta. Usa massimo 8000px.'
                ),
            ])
            ->setRequired(false)
            ->setHelp('Carica JPG, PNG o WebP fino a 20MB. Il sistema ridimensiona e comprime automaticamente il file.')
            ->hideOnIndex();
        yield TextField::new('imageUrl', 'File salvato')
            ->setDisabled()
            ->setHelp('Percorso relativo salvato nel database.')
            ->hideOnForm()
            ->hideOnIndex();
        yield BooleanField::new('isSpecial', 'Speciale');
        yield BooleanField::new('isEnabled', 'Abilitato');
        yield DateTimeField::new('createdAt', 'Creata il')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Aggiornata il')->hideOnForm();
    }
}
