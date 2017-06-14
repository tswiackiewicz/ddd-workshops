<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\UserRepositoryException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

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
     * @param UserId $id
     * @return User
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getById(UserId $id): User;
}