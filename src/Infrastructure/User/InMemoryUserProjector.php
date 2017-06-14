<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\{
    UserActivatedEvent, UserDisabledEvent, UserEnabledEvent, UserPasswordChangedEvent, UserRegisteredEvent, UserUnregisteredEvent
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserProjector;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserRegistry;
use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryStorage;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\AggregateId;

/**
 * Class InMemoryUserProjector
 * @package TSwiackiewicz\AwesomeApp\Infrastructure\User
 */
class InMemoryUserProjector implements UserProjector
{
    /**
     * @var UserRegistry
     */
    private $registry;

    /**
     * InMemoryUserProjector constructor.
     * @param UserRegistry $registry
     */
    public function __construct(UserRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param UserActivatedEvent $event
     */
    public function projectUserActivated(UserActivatedEvent $event): void
    {
        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'uuid' => $event->getId()->getAggregateId(),
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
                'uuid' => $event->getId()->getAggregateId(),
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
                'uuid' => $event->getId()->getAggregateId(),
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
                'uuid' => $event->getId()->getAggregateId(),
                'id' => $event->getId()->getId(),
                'password' => $event->getPassword()
            ]
        );
    }

    /**
     * @param UserRegisteredEvent $event
     * @return UserId
     */
    public function projectUserRegistered(UserRegisteredEvent $event): UserId
    {
        $aggregateId = $event->getId()->getAggregateId();
        $id = InMemoryStorage::nextIdentity(InMemoryStorage::TYPE_USER);

        /** @var UserId|AggregateId $registeredUserId */
        $registeredUserId = UserId::fromString($aggregateId)->setId($id);

        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'uuid' => $registeredUserId->getAggregateId(),
                'id' => $registeredUserId->getId(),
                'login' => $event->getLogin(),
                'password' => $event->getPassword(),
                'hash' => $event->getHash(),
                'active' => $event->isActive(),
                'enabled' => $event->isEnabled()
            ]
        );

        $this->registry->put($event->getLogin(), $registeredUserId);

        return $registeredUserId;
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