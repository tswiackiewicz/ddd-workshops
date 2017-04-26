<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    User, UserRepository
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class InMemoryUserRepository
 * @package TSwiackiewicz\AwesomeApp\Infrastructure\User
 */
class InMemoryUserRepository implements UserRepository
{
    /**
     * @return UserId
     */
    public function nextIdentity(): UserId
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return UserId::nullInstance();
    }

    /**
     * @param UserLogin $login
     * @param string $hash
     * @return User
     * @throws UserNotFoundException
     */
    public function findByHash(UserLogin $login, string $hash): User
    {
        // TODO: Implement findByHash() method.
    }

    /**
     * @param UserLogin $login
     * @return User
     * @throws UserNotFoundException
     */
    public function findByLogin(UserLogin $login): User
    {
        // TODO: Implement findByLogin() method.
    }

    /**
     * @param UserLogin $login
     * @return bool
     */
    public function exists(UserLogin $login): bool
    {
        // TODO: Implement exists() method.
    }

    /**
     * @param User $user
     * @return UserId
     */
    public function save(User $user): UserId
    {
        // TODO: Implement save() method.
    }

    /**
     * @param User $user
     */
    public function remove(User $user): void
    {
        // TODO: Implement remove() method.
    }
}