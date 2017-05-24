<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserActivatedEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserDisabledEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserEnabledEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserPasswordChangedEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserRegisteredEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserUnregisteredEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\PasswordException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\RuntimeException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\Event\AggregateHistory;

/**
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
class EventSourcedUser
{
    /**
     * @var UserId
     */
    private $id;

    /**
     * @var UserLogin
     */
    private $login;

    /**
     * @var UserPassword
     */
    private $password;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * EventSourcedUser constructor.
     * @param UserId $id
     */
    private function __construct(UserId $id)
    {
        $this->id = $id;
    }

    /**
     * @param AggregateHistory $aggregateHistory
     * @return EventSourcedUser
     */
    public static function reconstituteFrom(AggregateHistory $aggregateHistory): EventSourcedUser
    {
        $user = new static(
            UserId::fromInt($aggregateHistory->getAggregateId())
        );

        /** @var UserEvent[] $events */
        $events = $aggregateHistory->getDomainEvents();
        foreach ($events as $event) {
            $user->apply($event);
        }

        return $user;
    }

    /**
     * @param UserEvent $event
     */
    private function apply(UserEvent $event): void
    {
        $classParts = explode('\\', get_class($event));

        $method = 'when' . end($classParts);
        if ('Event' === substr($method, -5)) {
            $method = substr($method, 0, -5);
        }

        if (method_exists($this, $method)) {
            $this->$method($event);
        }
    }

    /**
     * Register new user
     *
     * @param UserId $id
     * @param UserLogin $username
     * @param UserPassword $password
     * @return EventSourcedUser
     */
    public static function register(UserId $id, UserLogin $username, UserPassword $password): EventSourcedUser
    {
        $user = new static($id);

        $user->whenUserRegistered(
            new UserRegisteredEvent($id, (string)$username, (string)$password)
        );

        return $user;
    }

    /**
     * @param UserRegisteredEvent $event
     */
    private function whenUserRegistered(UserRegisteredEvent $event): void
    {
        $this->login = new UserLogin($event->getLogin());
        $this->password = new UserPassword($event->getPassword());
        $this->active = $event->isActive();
        $this->enabled = $event->isEnabled();
    }

    /**
     * Activate user
     */
    public function activate(): void
    {
        if ($this->active) {
            throw new RuntimeException('User already activated');
        }

        $this->whenUserActivated(
            new UserActivatedEvent($this->id)
        );
    }

    /**
     * @param UserActivatedEvent $event
     */
    private function whenUserActivated(UserActivatedEvent $event): void
    {
        $this->active = $event->isActive();
        $this->enabled = $event->isEnabled();
    }

    /**
     * Enable user
     */
    public function enable(): void
    {
        if (!$this->active || $this->enabled) {
            throw new RuntimeException('Only active disabled user can be enabled');
        }

        $this->whenUserEnabled(
            new UserEnabledEvent($this->id)
        );
    }

    /**
     * @param UserEnabledEvent $event
     */
    private function whenUserEnabled(UserEnabledEvent $event): void
    {
        $this->enabled = $event->isEnabled();
    }

    /**
     * Disable user
     */
    public function disable(): void
    {
        if (!$this->active || !$this->enabled) {
            throw new RuntimeException('Only active enabled user can be disabled');
        }

        $this->whenUserDisabled(
            new UserDisabledEvent($this->id)
        );
    }

    /**
     * @param UserDisabledEvent $event
     */
    private function whenUserDisabled(UserDisabledEvent $event): void
    {
        $this->enabled = $event->isEnabled();
    }

    /**
     * @param UserPassword $password
     * @throws PasswordException
     */
    public function changePassword(UserPassword $password): void
    {
        if (!$this->active || !$this->enabled) {
            throw new RuntimeException('Only active enabled user can change password');
        }

        if ($this->password->equals($password)) {
            throw PasswordException::newPasswordEqualsWithCurrentPassword($this->id);
        }

        $this->whenUserPasswordChanged(
            new UserPasswordChangedEvent($this->id, (string)$password)
        );
    }

    /**
     * @param UserPasswordChangedEvent $event
     */
    private function whenUserPasswordChanged(UserPasswordChangedEvent $event): void
    {
        $this->password = new UserPassword($event->getPassword());
    }

    /**
     * Unregister user
     */
    public function unregister(): void
    {
        $this->whenUserUnregistered(
            new UserUnregisteredEvent($this->id)
        );
    }

    /**
     * @param UserUnregisteredEvent $event
     */
    private function whenUserUnregistered(UserUnregisteredEvent $event): void
    {
        $this->active = $event->isActive();
        $this->enabled = $event->isEnabled();
    }

    /**
     * @return UserId
     */
    public function getId(): UserId
    {
        return $this->id;
    }

    /**
     * Generate user hash string
     *
     * @return string
     */
    public function hash(): string
    {
        $hash = md5((string)$this->id->getId() . '::' . $this->login);

        // salt added to User's hash
        return substr($hash, 0, 8) .
            substr($hash, 24, 8) .
            substr($hash, 16, 8) .
            substr($hash, 8, 8);
    }
}

