<?php

namespace App\Controller\Admin;

use App\Entity\Drink;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

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
        yield TextField::new('name', 'Nome');
        yield TextareaField::new('description', 'Descrizione')->hideOnIndex();
        yield AssociationField::new('drinkCategory', 'Categoria drink')
            ->setRequired(false);
        yield BooleanField::new('isSpecial', 'Speciale');
        yield BooleanField::new('isEnabled', 'Abilitato');
        yield MoneyField::new('price', 'Prezzo')
            ->setCurrency('EUR')
            ->setNumDecimals(2)
            ->setStoredAsCents(false);
        yield UrlField::new('imageUrl', 'Immagine URL')->hideOnIndex();
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
