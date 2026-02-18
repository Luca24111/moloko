<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class EnvAdminUserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly string $adminUser,
        private readonly string $adminPassword
    ) {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        if ($this->adminUser === '' || $this->adminPassword === '') {
            $exception = new UserNotFoundException('Credenziali admin non configurate.');
            $exception->setUserIdentifier($identifier);
            throw $exception;
        }

        if (strcasecmp($identifier, $this->adminUser) !== 0) {
            $exception = new UserNotFoundException('Utente admin non trovato.');
            $exception->setUserIdentifier($identifier);
            throw $exception;
        }

        return new InMemoryUser($this->adminUser, $this->adminPassword, ['ROLE_ADMIN']);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof InMemoryUser) {
            throw new UnsupportedUserException(sprintf('Istanza utente non supportata: %s', $user::class));
        }

        return new InMemoryUser($this->adminUser, $this->adminPassword, ['ROLE_ADMIN']);
    }

    public function supportsClass(string $class): bool
    {
        return InMemoryUser::class === $class || is_subclass_of($class, InMemoryUser::class);
    }
}
