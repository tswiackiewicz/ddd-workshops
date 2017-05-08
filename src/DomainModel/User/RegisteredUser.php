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
class RegisteredUser extends User
{
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
        parent::__construct($id, $login, $password);
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
     * @param UserId $id ,
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
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }
}