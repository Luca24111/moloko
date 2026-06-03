<?php

namespace App\Controller\Admin;

use App\Entity\Drink;
use App\Service\ManagedMediaStorage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image as ImageConstraint;

abstract class AbstractDrinkCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Drink::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $label = $this->isAlcoholicGroup() ? 'Drink alcolici' : 'Drink analcolici';

        return $crud
            ->setEntityLabelInPlural($label)
            ->setEntityLabelInSingular('Drink')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['name', 'description', 'drinkCategory.name']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nome')->setRequired(false);
        yield TextareaField::new('description', 'Descrizione')
            ->setRequired(false)
            ->hideOnIndex();
        yield AssociationField::new('drinkCategory', 'Categoria drink')
            ->setRequired(false);
        yield ChoiceField::new('beerServingType', 'Formato birra')
            ->setChoices([
                'Alla spina' => 'draft',
                'In bottiglietta' => 'bottle',
            ])
            ->setRequired(false)
            ->setHelp('Usalo solo per la categoria Birre: crea le divisioni nel frontend.');
        yield BooleanField::new('isSpecial', 'Speciale');
        yield BooleanField::new('isEnabled', 'Abilitato');
        yield MoneyField::new('price', 'Prezzo')
            ->setCurrency('EUR')
            ->setNumDecimals(2)
            ->setStoredAsCents(false)
            ->setRequired(false);
        yield MoneyField::new('beerSmallPrice', 'Prezzo birra piccola')
            ->setCurrency('EUR')
            ->setNumDecimals(2)
            ->setStoredAsCents(false)
            ->setRequired(false)
            ->setHelp('Usalo per le birre alla spina con doppio prezzo.');
        yield MoneyField::new('beerMediumPrice', 'Prezzo birra media')
            ->setCurrency('EUR')
            ->setNumDecimals(2)
            ->setStoredAsCents(false)
            ->setRequired(false)
            ->setHelp('Usalo per le birre alla spina con doppio prezzo.');
        yield ImageField::new('imageUrl', 'Immagine')
            ->setBasePath((string) ManagedMediaStorage::basePathFor(Drink::class))
            ->onlyOnIndex();
        yield ImageField::new('imageUrl', 'Immagine')
            ->setBasePath((string) ManagedMediaStorage::basePathFor(Drink::class))
            ->setUploadDir((string) ManagedMediaStorage::uploadDirFor(Drink::class))
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
        yield DateTimeField::new('createdAt', 'Creato il')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Aggiornato il')->hideOnForm();
    }

    public function createEntity(string $entityFqcn): Drink
    {
        $drink = new Drink();
        $drink->setIsAlcoholic($this->isAlcoholicGroup());

        return $drink;
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        return $queryBuilder
            ->andWhere('entity.isAlcoholic = :alcoholic')
            ->setParameter('alcoholic', $this->isAlcoholicGroup());
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Drink) {
            $entityInstance->setIsAlcoholic($this->isAlcoholicGroup());
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Drink) {
            $entityInstance->setIsAlcoholic($this->isAlcoholicGroup());
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    abstract protected function isAlcoholicGroup(): bool;
}
