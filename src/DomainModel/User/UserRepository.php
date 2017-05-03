<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\{
    Exception\UserRepositoryException, UserId
};

/**
 * When User (Application) Service is split by User's BC (registered vs active),
 * we should divide UserRepository into RegisteredUserRepository and ActiveUserRepository,
 * with common UserRepository if needed
 *
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
     * @return RegisteredUser
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getRegisteredUserByHash(string $hash): RegisteredUser;

    /**
     * @param UserId $id
     * @return ActiveUser
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getActiveUserById(UserId $id): ActiveUser;

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