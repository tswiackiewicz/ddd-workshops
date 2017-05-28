<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class RegisteredUser
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
class RegisteredUser
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
     * RegisteredUser constructor.
     * @param UserId $id
     * @param UserLogin $login
     * @param UserPassword $password
     * @param bool $active
     */
    public function __construct(UserId $id, UserLogin $login, UserPassword $password, bool $active)
    {
        $this->id = $id;
        $this->login = $login;
        $this->password = $password;
        $this->active = $active;
    }

    /**
     * @param array $user
     * @return RegisteredUser
     * @throws InvalidArgumentException
     */
    public static function fromNative(array $user): RegisteredUser
    {
        return new static(
            UserId::fromInt($user['id']),
            new UserLogin($user['login']),
            new UserPassword($user['password']),
            isset($user['active']) && true === $user['active']
        );
    }

    /**
     * @param UserId $id
     * @param UserLogin $username
     * @param UserPassword $password
     * @return RegisteredUser
     */
    public static function register(UserId $id, UserLogin $username, UserPassword $password): RegisteredUser
    {
        return new static($id, $username, $password, false);
    }

    /**
     * Activate registered user
     */
    public function activate(): void
    {
        $this->active = true;
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
    public function isActive(): bool
    {
        return $this->active;
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