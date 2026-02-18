<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

final class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Eventi')
            ->setEntityLabelInSingular('Evento')
            ->setDefaultSort(['startsAt' => 'DESC'])
            ->setSearchFields(['title', 'description', 'location']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('title', 'Titolo');
        yield TextareaField::new('description', 'Descrizione')->hideOnIndex();
        yield DateTimeField::new('startsAt', 'Inizio');
        yield DateTimeField::new('endsAt', 'Fine')->setRequired(false);
        yield TextField::new('location', 'Location');
        yield MoneyField::new('ticketPrice', 'Prezzo biglietto')
            ->setCurrency('EUR')
            ->setStoredAsCents(false)
            ->setNumDecimals(2)
            ->setRequired(false);
        yield UrlField::new('coverImageUrl', 'Cover URL')->hideOnIndex();
        yield BooleanField::new('isPublished', 'Pubblicato');
        yield DateTimeField::new('createdAt', 'Creato il')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Aggiornato il')->hideOnForm();
    }
}
