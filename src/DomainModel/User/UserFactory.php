<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class UserFactory
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
class UserFactory
{
    /**
     * @param array $user
     * @return User
     * @throws InvalidArgumentException
     */
    public function fromNative(array $user): User
    {
        if (isset($user['active']) && true === $user['active']) {
            return $this->activeUserFromNative($user);
        }

        return $this->registeredUserFromNative($user);
    }

    /**
     * @param array $user
     * @return ActiveUser
     * @throws InvalidArgumentException
     */
    public function activeUserFromNative(array $user): ActiveUser
    {
        return new ActiveUser(
            UserId::fromInt($user['id']),
            new UserLogin($user['login']),
            new UserPassword($user['password']),
            isset($user['enabled']) && true === $user['enabled']
        );
    }

    /**
     * @param array $user
     * @return RegisteredUser
     * @throws InvalidArgumentException
     */
    public function registeredUserFromNative(array $user): RegisteredUser
    {
        return new RegisteredUser(
            UserId::fromInt($user['id']),
            new UserLogin($user['login']),
            new UserPassword($user['password']),
            isset($user['active']) && true === $user['active']
        );
    }
}