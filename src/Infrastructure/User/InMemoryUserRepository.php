<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    User, UserRepository
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
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
        return UserId::nullInstance();
    }

    /**
     * @param string $hash
     * @return User
     * @throws UserNotFoundException
     */
    public function getByHash(string $hash): User
    {
        // TODO: Implement getByHash() method.
    }

    /**
     * @param string $login
     * @return User
     * @throws UserNotFoundException
     */
    public function getByLogin(string $login): User
    {
        // TODO: Implement getByLogin() method.
    }

    /**
     * @param string $login
     * @return bool
     */
    public function exists(string $login): bool
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
     * @param UserId $id
     */
    public function remove(UserId $id): void
    {
        // TODO: Implement remove() method.
    }
}