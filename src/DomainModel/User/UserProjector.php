<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserActivatedEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserDisabledEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserEnabledEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserPasswordChangedEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserRegisteredEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserUnregisteredEvent;

/**
 * Interface UserProjector
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
interface UserProjector
{
    /**
     * @param UserActivatedEvent $event
     */
    public function projectUserActivated(UserActivatedEvent $event): void;

    /**
     * @param UserDisabledEvent $event
     */
    public function projectUserDisabled(UserDisabledEvent $event): void;

    /**
     * @param UserEnabledEvent $event
     */
    public function projectUserEnabled(UserEnabledEvent $event): void;

    /**
     * @param UserPasswordChangedEvent $event
     */
    public function projectUserPasswordChanged(UserPasswordChangedEvent $event): void;

    /**
     * @param UserRegisteredEvent $event
     */
    public function projectUserRegistered(UserRegisteredEvent $event): void;

    /**
     * @param UserUnregisteredEvent $event
     */
    public function projectUserUnregistered(UserUnregisteredEvent $event): void;
}