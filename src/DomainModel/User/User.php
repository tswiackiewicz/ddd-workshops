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
use TSwiackiewicz\DDD\Event\EventBus;
use TSwiackiewicz\DDD\EventSourcing\AggregateHistory;

/**
 * Class User
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
class User
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
     * @return User
     */
    public static function reconstituteFrom(AggregateHistory $aggregateHistory): User
    {
        /** @var UserId $userId */
        $userId = UserId::fromInt($aggregateHistory->getAggregateId()->getId());

        $user = new static($userId);

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
     * @return User
     */
    public static function register(UserId $id, UserLogin $username, UserPassword $password): User
    {
        $user = new static($id);

        $user->publishEvent(
            new UserRegisteredEvent($id, (string)$username, (string)$password)
        );

        return $user;
    }

    /**
     * @param UserEvent $event
     */
    private function publishEvent(UserEvent $event): void
    {
        $this->apply($event);
        EventBus::publish($event);
    }

    /**
     * Activate user
     */
    public function activate(): void
    {
        if ($this->active) {
            throw new RuntimeException('User already activated');
        }

        $this->publishEvent(
            new UserActivatedEvent($this->id)
        );
    }

    /**
     * Enable user
     */
    public function enable(): void
    {
        if (!$this->active || $this->enabled) {
            throw new RuntimeException('Only active disabled user can be enabled');
        }

        $this->publishEvent(
            new UserEnabledEvent($this->id)
        );
    }

    /**
     * Disable user
     */
    public function disable(): void
    {
        if (!$this->active || !$this->enabled) {
            throw new RuntimeException('Only active enabled user can be disabled');
        }

        $this->publishEvent(
            new UserDisabledEvent($this->id)
        );
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

        $this->publishEvent(
            new UserPasswordChangedEvent($this->id, (string)$password)
        );
    }

    /**
     * Unregister user
     */
    public function unregister(): void
    {
        $this->publishEvent(
            new UserUnregisteredEvent($this->id)
        );
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
     * @param UserActivatedEvent $event
     */
    private function whenUserActivated(UserActivatedEvent $event): void
    {
        $this->active = $event->isActive();
        $this->enabled = $event->isEnabled();
    }

    /**
     * @param UserEnabledEvent $event
     */
    private function whenUserEnabled(UserEnabledEvent $event): void
    {
        $this->enabled = $event->isEnabled();
    }

    /**
     * @param UserDisabledEvent $event
     */
    private function whenUserDisabled(UserDisabledEvent $event): void
    {
        $this->enabled = $event->isEnabled();
    }

    /**
     * @param UserPasswordChangedEvent $event
     */
    private function whenUserPasswordChanged(UserPasswordChangedEvent $event): void
    {
        $this->password = new UserPassword($event->getPassword());
    }

    /**
     * @param UserUnregisteredEvent $event
     */
    private function whenUserUnregistered(UserUnregisteredEvent $event): void
    {
        $this->active = $event->isActive();
        $this->enabled = $event->isEnabled();
    }
}

