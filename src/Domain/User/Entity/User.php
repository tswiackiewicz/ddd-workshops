<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Domain\User\Entity;

use TSwiackiewicz\AwesomeApp\Domain\User\Exception\PasswordException;
use TSwiackiewicz\AwesomeApp\Domain\User\Exception\UserException;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserLogin;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserPassword;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserStatus;
use TSwiackiewicz\AwesomeApp\SharedKernel\Exception\InvalidArgumentException;

class User
{
    public function __construct(
        private UserId $id,
        private UserLogin $login,
        private UserPassword $password,
        private UserStatus $status
    ) {
    }

    public static function fromNative(array $user): User
    {
        $status = match(true) {
            !(bool)($user['active'] ?? false) => UserStatus::INACTIVE,
            (bool)($user['enabled'] ?? false) => UserStatus::ACTIVE,
            default => UserStatus::DISABLED,
        };

        return new static(
            UserId::fromInt($user['id']),
            new UserLogin($user['login']),
            new UserPassword($user['password']),
            $status
        );
    }

    public static function register(UserId $id, UserLogin $username, UserPassword $password): User
    {
        return new static($id, $username, $password, UserStatus::INACTIVE);
    }

    public function activate(): void
    {
        if ($this->status !== UserStatus::INACTIVE) {
            throw UserException::alreadyActivated($this->id);
        }

        $this->status = UserStatus::ACTIVE;
    }

    public function enable(): void
    {
        if ($this->status !== UserStatus::DISABLED) {
            throw UserException::enableNotAllowed($this->id);
        }

        $this->status = UserStatus::ACTIVE;
    }

    public function disable(): void
    {
        if ($this->status !== UserStatus::ACTIVE) {
            throw UserException::disableNotAllowed($this->id);
        }

        $this->status = UserStatus::DISABLED;
    }

    public function changePassword(UserPassword $password): void
    {
        if ($this->status !== UserStatus::ACTIVE) {
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

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->status !== UserStatus::INACTIVE;
    }

    public function isEnabled(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

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
