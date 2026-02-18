<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserCrudController extends AbstractCrudController
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Utenti')
            ->setEntityLabelInSingular('Utente')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['email', 'fullName']);
    }

    public function configureFields(string $pageName): iterable
    {
        $password = TextField::new('plainPassword', 'Password')
            ->setFormType(PasswordType::class)
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->setHelp('Lascia vuoto in modifica se non vuoi cambiare password.')
            ->onlyOnForms();

        yield IdField::new('id')->hideOnForm();
        yield TextField::new('fullName', 'Nome completo')->setRequired(false);
        yield EmailField::new('email', 'Email');
        yield ChoiceField::new('roles', 'Ruoli')
            ->setChoices([
                'Utente' => 'ROLE_USER',
                'Admin' => 'ROLE_ADMIN',
            ])
            ->allowMultipleChoices()
            ->renderAsBadges([
                'ROLE_ADMIN' => 'danger',
                'ROLE_USER' => 'success',
            ]);
        yield BooleanField::new('isActive', 'Attivo');
        yield $password;
        yield DateTimeField::new('createdAt', 'Creato il')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Aggiornato il')->hideOnForm();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            $this->hashPasswordIfNeeded($entityInstance);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            $this->hashPasswordIfNeeded($entityInstance);
            $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function hashPasswordIfNeeded(User $user): void
    {
        $plainPassword = $user->getPlainPassword();

        if (is_string($plainPassword) && $plainPassword !== '') {
            $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
            $user->eraseCredentials();
        }
    }
}
