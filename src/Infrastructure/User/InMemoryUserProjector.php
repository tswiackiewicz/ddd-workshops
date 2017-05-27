<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\{
    UserActivatedEvent, UserDisabledEvent, UserEnabledEvent, UserPasswordChangedEvent, UserRegisteredEvent, UserUnregisteredEvent
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserProjector;
use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryStorage;

/**
 * Class InMemoryUserProjector
 * @package TSwiackiewicz\AwesomeApp\Infrastructure\User
 */
class InMemoryUserProjector implements UserProjector
{
    /**
     * @param UserActivatedEvent $event
     */
    public function projectUserActivated(UserActivatedEvent $event): void
    {
        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'id' => $event->getId()->getId(),
                'active' => $event->isActive(),
                'enabled' => $event->isEnabled()
            ]
        );
    }

    /**
     * @param UserDisabledEvent $event
     */
    public function projectUserDisabled(UserDisabledEvent $event): void
    {
        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'id' => $event->getId()->getId(),
                'enabled' => $event->isEnabled()
            ]
        );
    }

    /**
     * @param UserEnabledEvent $event
     */
    public function projectUserEnabled(UserEnabledEvent $event): void
    {
        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'id' => $event->getId()->getId(),
                'enabled' => $event->isEnabled()
            ]
        );
    }

    /**
     * @param UserPasswordChangedEvent $event
     */
    public function projectUserPasswordChanged(UserPasswordChangedEvent $event): void
    {
        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'id' => $event->getId()->getId(),
                'password' => $event->getPassword()
            ]
        );
    }

    /**
     * @param UserRegisteredEvent $event
     */
    public function projectUserRegistered(UserRegisteredEvent $event): void
    {
        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'id' => $event->getId()->getId(),
                'login' => $event->getLogin(),
                'password' => $event->getPassword(),
                'active' => $event->isActive(),
                'enabled' => $event->isEnabled()
            ]
        );
    }

    /**
     * @param UserUnregisteredEvent $event
     */
    public function projectUserUnregistered(UserUnregisteredEvent $event): void
    {
        InMemoryStorage::removeById(
            InMemoryStorage::TYPE_USER,
            $event->getId()->getId()
        );
    }
}