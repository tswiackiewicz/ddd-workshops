<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserEnabledEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserUnregisteredEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\Event\EventBus;

/**
 * Example of two different User's Bounded Contexts
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
     * Enable user
     */
    public function enable(): void
    {
        $this->enabled = true;

        EventBus::publish(
            new UserEnabledEvent($this->id, (string)$this->login)
        );
    }

    /**
     * Disable user
     */
    public function disable(): void
    {
        $this->enabled = false;

        // TODO: publish UserDisabledEvent
    }

    /**
     * @param UserPassword $password
     */
    public function changePassword(UserPassword $password): void
    {
        $this->password = $password;

        // TODO: publish UserPasswordChangedEvent
    }

    /**
     * Unregister user
     */
    public function unregister(): void
    {
        $this->enabled = false;

        EventBus::publish(
            new UserUnregisteredEvent($this->id, (string)$this->login)
        );
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}