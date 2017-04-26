<?php

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\ReadModel\User\UserDTO;
use TSwiackiewicz\AwesomeApp\ReadModel\User\UserQuery;
use TSwiackiewicz\AwesomeApp\ReadModel\User\UserReadModelRepository;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class InMemoryUserReadModelRepository
 * @package TSwiackiewicz\AwesomeApp\Infrastructure\User
 */
class InMemoryUserReadModelRepository implements UserReadModelRepository
{
    /**
     * @var array
     */
    private static $users = [];

    /**
     * @var int
     */
    private static $nextUserIdentity = 1;

    /**
     * @param array $user
     */
    public static function addUser(array $user): void
    {
        $user['id'] = self::$nextUserIdentity;
        self::$users[self::$nextUserIdentity++] = $user;
    }

    /**
     * @param UserId $id
     * @param array $user
     */
    public static function setUser(UserId $id, array $user): void
    {
        $user['id'] = $id->getId();
        self::$users[$id->getId()] = $user;
    }

    /**
     * Clear defined users' set
     */
    public static function clear(): void
    {
        self::$users = [];
    }

    /**
     * @param UserId $id
     * @return null|UserDTO
     */
    public function findById(UserId $id): ?UserDTO
    {
        return isset(self::$users[$id->getId()]) ? UserDTO::fromArray(self::$users[$id->getId()]) : null;
    }

    /**
     * @param UserQuery $query
     * @return UserDTO[]
     */
    public function findByQuery(UserQuery $query): array
    {
        $users = [];

        foreach (self::$users as $user) {
            if ($query->isActive() === $user['active'] && $query->isEnabled() === $user['enabled']) {
                $users[] = UserDTO::fromArray($user);
            }
        }

        return $users;
    }

    /**
     * @return UserDTO[]
     */
    public function getAllUsers(): array
    {
        $users = [];

        foreach (self::$users as $user) {
            $users[] = UserDTO::fromArray($user);
        }

        return $users;
    }

}