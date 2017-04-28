<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryStorage;
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
    private const USER_STORAGE_TYPE = 'user';

    /**
     * @param UserId $id
     * @return null|UserDTO
     */
    public function findById(UserId $id): ?UserDTO
    {
        $users = InMemoryStorage::fetchAll(self::USER_STORAGE_TYPE);

        return isset($users[$id->getId()]) ? UserDTO::fromArray($users[$id->getId()]) : null;
    }

    /**
     * @param UserQuery $query
     * @return UserDTO[]
     */
    public function findByQuery(UserQuery $query): array
    {
        $userDTOCollection = [];

        $users = InMemoryStorage::fetchAll(self::USER_STORAGE_TYPE);
        foreach ($users as $user) {
            if ($query->isActive() === $user['active'] && $query->isEnabled() === $user['enabled']) {
                $userDTOCollection[] = UserDTO::fromArray($user);
            }
        }

        return $userDTOCollection;
    }

    /**
     * @return UserDTO[]
     */
    public function getUsers(): array
    {
        $userDTOCollection = [];

        $users = InMemoryStorage::fetchAll(self::USER_STORAGE_TYPE);
        foreach ($users as $user) {
            $userDTOCollection[] = UserDTO::fromArray($user);
        }

        return $userDTOCollection;
    }

}