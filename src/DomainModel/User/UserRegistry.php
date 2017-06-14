<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\UserRegistryException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Interface UserRegistry
 *
 * @see http://codebetter.com/gregyoung/2010/08/12/eventual-consistency-and-set-validation/
 * @sse https://seabites.wordpress.com/2010/11/11/consistent-indexes-constraints/
 *
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
interface UserRegistry
{
    /**
     * @param string $login
     * @return bool
     */
    public function exists(string $login): bool;

    /**
     * @param string $login
     * @return UserId
     * @throws UserRegistryException
     * @throws UserNotFoundException
     */
    public function getByLogin(string $login): UserId;

    /**
     * @param string $hash
     * @return UserId
     * @throws UserRegistryException
     * @throws UserNotFoundException
     */
    public function getByHash(string $hash): UserId;

    /**
     * @param string $login
     * @param UserId $id
     */
    public function put(string $login, UserId $id): void;
}