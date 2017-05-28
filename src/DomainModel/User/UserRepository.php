<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\{
    Exception\UserRepositoryException, UserId
};

/**
 * Interface UserRepository
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
interface UserRepository
{
    /**
     * @return UserId
     */
    public function nextIdentity(): UserId;

    /**
     * @param string $login
     * @return bool
     */
    public function exists(string $login): bool;

    /**
     * @param UserId $id
     * @return User
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getById(UserId $id): User;

    /**
     * @param string $hash
     * @return User
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getByHash(string $hash): User;

    /**
     * @param User $user
     * @return UserId
     * @throws UserRepositoryException
     */
    public function save(User $user): UserId;

    /**
     * @param UserId $id
     */
    public function remove(UserId $id): void;
}