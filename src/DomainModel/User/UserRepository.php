<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;

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
     * @param UserLogin $login
     * @param string $hash
     * @return User
     * @throws UserNotFoundException
     */
    public function findByHash(UserLogin $login, string $hash): User;

    /**
     * @param UserLogin $login
     * @return User
     * @throws UserNotFoundException
     */
    public function findByLogin(UserLogin $login): User;

    /**
     * @param UserLogin $login
     * @return bool
     */
    public function exists(UserLogin $login): bool;

    /**
     * @param User $user
     * @return UserId
     */
    public function save(User $user): UserId;

    /**
     * @param User $user
     */
    public function remove(User $user): void;
}