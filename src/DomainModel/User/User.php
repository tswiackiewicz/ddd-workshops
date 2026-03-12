<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\PasswordException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

class User
{
    public function __construct(
        private UserId $id,
        private UserLogin $login,
        private UserPassword $password,
        private bool $active,
        private bool $enabled
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function fromNative(array $user): User
    {
        return new static(
            UserId::fromInt($user['id']),
            new UserLogin($user['login']),
            new UserPassword($user['password']),
            isset($user['active']) && true === $user['active'],
            isset($user['enabled']) && true === $user['enabled']
        );
    }

    /**
     * Register new user
     */
    public static function register(UserId $id, UserLogin $username, UserPassword $password): User
    {
        return new static($id, $username, $password, false, false);
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
    }

    /**
     * Change user's password
     *
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
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getLogin(): UserLogin
    {
        return $this->login;
    }

    public function getPassword(): UserPassword
    {
        return $this->password;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Generate user hash string
     */
    public function hash(): string
    {
        $hash = md5('::' . $this->login . '::');

        // salt added to User's hash
        return substr($hash, 0, 8) .
            substr($hash, 24, 8) .
            substr($hash, 16, 8) .
            substr($hash, 8, 8);
    }
}
