<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Exception\PasswordException, Exception\UserException, Password\UserPassword
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\{
    UserActivatedEvent, UserDisabledEvent, UserEnabledEvent, UserPasswordChangedEvent, UserRegisteredEvent, UserUnregisteredEvent
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\{
    Exception\InvalidArgumentException, UserId
};
use TSwiackiewicz\DDD\AggregateRoot;

/**
 * Class User
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
class User extends AggregateRoot
{
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
     * User constructor.
     * @param UserId $id
     * @param UserLogin $login
     * @param UserPassword $password
     * @param bool $active
     * @param bool $enabled
     */
    public function __construct(UserId $id, UserLogin $login, UserPassword $password, bool $active, bool $enabled)
    {
        parent::__construct($id);
        $this->login = $login;
        $this->password = $password;
        $this->active = $active;
        $this->enabled = $enabled;
    }

    /**
     * @param array $user
     * @return User
     * @throws InvalidArgumentException
     */
    public static function fromNative(array $user): User
    {
        /** @var UserId $userId */
        $userId = UserId::fromString($user['uuid'])->setId($user['id']);

        return new static(
            $userId,
            new UserLogin($user['login']),
            new UserPassword($user['password']),
            isset($user['active']) && true === $user['active'],
            isset($user['enabled']) && true === $user['enabled']
        );
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
        $user = new static($id, $username, $password, false, false);

        $user->recordThat(
            new UserRegisteredEvent($id, (string)$username, (string)$password, $user->doHash((string)$username))
        );

        return $user;
    }

    /**
     * @param string $login
     * @return string
     */
    private function doHash(string $login): string
    {
        $hash = md5('::' . $login . '::');
        // salt added to User's hash
        return substr($hash, 0, 8) .
            substr($hash, 24, 8) .
            substr($hash, 16, 8) .
            substr($hash, 8, 8);
    }

    /**
     * Activate user
     *
     * @throws UserException
     */
    public function activate(): void
    {
        if ($this->active) {
            throw UserException::alreadyActivated($this->id);
        }

        $this->active = true;
        $this->enabled = true;

        $this->recordThat(
            new UserActivatedEvent($this->id)
        );
    }

    /**
     * Enable user
     *
     * @throws UserException
     */
    public function enable(): void
    {
        if (!$this->active || $this->enabled) {
            throw UserException::enableNotAllowed($this->id);
        }

        $this->enabled = true;

        $this->recordThat(
            new UserEnabledEvent($this->id)
        );
    }

    /**
     * Disable user
     *
     * @throws UserException
     */
    public function disable(): void
    {
        if (!$this->active || !$this->enabled) {
            throw UserException::disableNotAllowed($this->id);
        }

        $this->enabled = false;

        $this->recordThat(
            new UserDisabledEvent($this->id)
        );
    }

    /**
     * Change user's password
     *
     * @param UserPassword $password
     * @throws UserException
     * @throws PasswordException
     */
    public function changePassword(UserPassword $password): void
    {
        if (!$this->active || !$this->enabled) {
            throw UserException::passwordChangeNotAllowed($this->id);
        }

        if ($this->password->equals($password)) {
            throw PasswordException::newPasswordEqualsWithCurrentPassword($this->id);
        }

        $this->password = $password;

        $this->recordThat(
            new UserPasswordChangedEvent($this->id, (string)$password)
        );
    }

    /**
     * Unregister user
     */
    public function unregister(): void
    {
        $this->active = false;
        $this->enabled = false;

        $this->recordThat(
            new UserUnregisteredEvent($this->id)
        );
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
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Generate user hash string
     *
     * @return string
     */
    public function hash(): string
    {
        return $this->doHash((string)$this->login);
    }

}