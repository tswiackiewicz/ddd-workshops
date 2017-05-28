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
class ActiveUser
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
        $this->id = $id;
        $this->login = $login;
        $this->password = $password;
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

        EventBus::publish(new UserEnabledEvent($this->id));
    }

    /**
     * Disable user
     */
    public function disable(): void
    {
        $this->enabled = false;

        EventBus::publish(new UserDisabledEvent($this->id));
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

        $this->password = $password;

        EventBus::publish(new UserPasswordChangedEvent($this->id, (string)$this->password));
    }

    /**
     * Unregister user
     */
    public function unregister(): void
    {
        $this->enabled = false;

        EventBus::publish(new UserUnregisteredEvent($this->id));
    }

    /**
     * @return UserId
     */
    public function getId(): UserId
    {
        return $this->id;
    }

    /**
     * @return UserLogin
     */
    public function getLogin(): UserLogin
    {
        return $this->login;
    }

    /**
     * @return UserPassword
     */
    public function getPassword(): UserPassword
    {
        return $this->password;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return string
     */
    public function hash(): string
    {
        $hash = md5($this->login . '::' . $this->password);

        // salt added to User's hash
        return substr($hash, 0, 8) . substr($hash, 24, 8) .
            substr($hash, 16, 8) . substr($hash, 8, 8);
    }
}