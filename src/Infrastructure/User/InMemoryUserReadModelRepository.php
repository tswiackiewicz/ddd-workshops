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
    /**
     * @param UserId $id
     * @return null|UserDTO
     */
    public function findById(UserId $id): ?UserDTO
    {
        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);

        print_r($users);

        return isset($users[$id->getId()]) ? UserDTO::fromArray($users[$id->getId()]) : null;
    }

    /**
     * @param UserQuery $query
     * @return UserDTO[]
     */
    public function findByQuery(UserQuery $query): array
    {
        $userDTOCollection = [];

        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);
        foreach ($users as $user) {
            if (isset($user['active']) && $query->isActive() === $user['active'] &&
                isset($user['enabled']) && $query->isEnabled() === $user['enabled']
            ) {
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

        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);
        foreach ($users as $user) {
            $userDTOCollection[] = UserDTO::fromArray($user);
        }

        return $userDTOCollection;
    }

}