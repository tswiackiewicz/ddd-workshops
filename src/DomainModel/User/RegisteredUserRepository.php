<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\{
    Exception\UserRepositoryException, UserId
};

/**
 * Interface RegisteredUserRepository
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
interface RegisteredUserRepository
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
     * @return RegisteredUser
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getById(UserId $id): RegisteredUser;

    /**
     * @param string $hash
     * @return RegisteredUser
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getByHash(string $hash): RegisteredUser;

    /**
     * @param RegisteredUser $user
     * @return UserId
     * @throws UserRepositoryException
     */
    public function save(RegisteredUser $user): UserId;
}