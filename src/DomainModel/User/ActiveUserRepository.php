<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\{
    Exception\UserRepositoryException, UserId
};

/**
 * Interface ActiveUserRepository
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
interface ActiveUserRepository
{
    /**
     * @param UserId $id
     * @return ActiveUser
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getById(UserId $id): ActiveUser;

    /**
     * @param ActiveUser $user
     * @throws UserRepositoryException
     */
    public function save(ActiveUser $user): void;

    /**
     * @param UserId $id
     */
    public function remove(UserId $id): void;
}