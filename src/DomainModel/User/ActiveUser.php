<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\{
    UserDisabledEvent, UserEnabledEvent, UserPasswordChangedEvent, UserUnregisteredEvent
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\PasswordException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\{
    Exception\InvalidArgumentException, UserId
};
use TSwiackiewicz\DDD\Event\EventBus;

/**
 * One of two different User's Bounded Contexts
 * It can be organized within same or various (sub-)namespaces
 *
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
class ActiveUser extends User
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * ActiveUser constructor.
     * @param UserId $id
     * @param UserLogin $login
     * @param UserPassword $password
     * @param bool $enabled
     */
    public function __construct(UserId $id, UserLogin $login, UserPassword $password, bool $enabled)
    {
        parent::__construct($id, $login, $password);
        $this->enabled = $enabled;
    }

    /**
     * @param array $user
     * @return ActiveUser
     * @throws InvalidArgumentException
     */
    public static function fromNative(array $user): ActiveUser
    {
        return new static(
            UserId::fromInt($user['id']),
            new UserLogin($user['login']),
            new UserPassword($user['password']),
            isset($user['enabled']) && true === $user['enabled']
        );
    }

    /**
     * @param AggregateHistory $aggregateHistory
     * @return ActiveUser
     */
    public static function reconstituteFrom(AggregateHistory $aggregateHistory): ActiveUser
    {
        $user = new static(
            UserId::fromInt($aggregateHistory->getAggregateId())
        );

        $events = $aggregateHistory->getDomainEvents();
        foreach ($events as $event)
        {
            $user->apply($event);
        }

        return $user;
    }

    /**
     * Enable user
     */
    public function enable(): void
    {
        $event = new UserEnabledEvent($this->id);

        $this->applyUserEnabledEvent($event);
        EventBus::publish($event);
    }

    /**
     * @param UserEnabledEvent $event
     */
    private function applyUserEnabledEvent(UserEnabledEvent $event): void
    {
        $this->enabled = $event->isEnabled();
    }

    /**
     * Disable user
     */
    public function disable(): void
    {
        $event = new UserDisabledEvent($this->id);

        $this->applyUserDisabledEvent($event);
        EventBus::publish($event);
    }

    /**
     * @param UserDisabledEvent $event
     */
    private function applyUserDisabledEvent(UserDisabledEvent $event): void
    {
        $this->enabled = $event->isEnabled();
    }

    /**
     * @param UserPassword $password
     * @throws PasswordException
     */
    public function changePassword(UserPassword $password): void
    {
        if ($this->password->equals($password)) {
            throw PasswordException::newPasswordEqualsWithCurrentPassword($this->id);
        }

        $event = new UserPasswordChangedEvent($this->id, (string)$password);

        $this->applyUserPasswordChangedEvent($event);
        EventBus::publish($event);
    }

    /**
     * @param UserPasswordChangedEvent $event
     */
    private function applyUserPasswordChangedEvent(UserPasswordChangedEvent $event): void
    {
        $this->password = new UserPassword($event->getPassword());
    }

    /**
     * Unregister user
     */
    public function unregister(): void
    {
        $event = new UserUnregisteredEvent($this->id);

        $this->applyUserUnregisteredEvent($event);
        EventBus::publish($event);
    }

    /**
     * @param UserUnregisteredEvent $event
     */
    public function applyUserUnregisteredEvent(UserUnregisteredEvent $event): void
    {
        $this->enabled = $event->isEnabled();
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}