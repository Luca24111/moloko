<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Service\ManagedMediaStorage;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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
        yield TextField::new('title', 'Titolo')->setRequired(false);
        yield TextareaField::new('description', 'Descrizione')
            ->setRequired(false)
            ->hideOnIndex();
        yield DateTimeField::new('startsAt', 'Inizio')->setRequired(false);
        yield DateTimeField::new('endsAt', 'Fine')->setRequired(false);
        yield TextField::new('location', 'Location')->setRequired(false);
        yield MoneyField::new('ticketPrice', 'Prezzo biglietto')
            ->setCurrency('EUR')
            ->setStoredAsCents(false)
            ->setNumDecimals(2)
            ->setRequired(false);
        yield ImageField::new('coverImageUrl', 'Cover')
            ->setBasePath((string) ManagedMediaStorage::basePathFor(Event::class))
            ->onlyOnIndex();
        yield ImageField::new('coverImageUrl', 'Cover')
            ->setBasePath((string) ManagedMediaStorage::basePathFor(Event::class))
            ->setUploadDir((string) ManagedMediaStorage::uploadDirFor(Event::class))
            ->setUploadedFileNamePattern(ManagedMediaStorage::UPLOAD_FILENAME_PATTERN)
            ->setRequired(false)
            ->setHelp('Carica un file dal dispositivo. Il file verra gestito e pulito automaticamente dal sistema.')
            ->hideOnIndex();
        yield TextField::new('coverImageUrl', 'File salvato')
            ->setDisabled()
            ->setHelp('Percorso relativo salvato nel database.')
            ->hideOnIndex();
        yield BooleanField::new('isPublished', 'Pubblicato');
        yield DateTimeField::new('createdAt', 'Creato il')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Aggiornato il')->hideOnForm();
    }
}
