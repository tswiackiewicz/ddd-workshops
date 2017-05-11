<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryStorage;
use TSwiackiewicz\AwesomeApp\ReadModel\User\UserDTO;
use TSwiackiewicz\AwesomeApp\ReadModel\User\UserPaginatedResult;
use TSwiackiewicz\AwesomeApp\ReadModel\User\UserQuery;
use TSwiackiewicz\AwesomeApp\ReadModel\User\UserReadModelRepository;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\Query\PaginatedResult;
use TSwiackiewicz\DDD\Query\QueryContext;

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

        return isset($users[$id->getId()]) ? UserDTO::fromArray($users[$id->getId()]) : null;
    }

    /**
     * @param UserQuery $query
     * @param null|QueryContext $context
     * @return PaginatedResult
     */
    public function findByQuery(UserQuery $query, ?QueryContext $context = null): PaginatedResult
    {
        $userDTOCollection = [];

        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);
        foreach ($users as $user) {
            if (isset($user['active'], $user['enabled']) &&
                $query->isActive() === $user['active'] &&
                $query->isEnabled() === $user['enabled']
            ) {
                $userDTOCollection[] = UserDTO::fromArray($user);
            }
        }

        return UserPaginatedResult::singlePage($userDTOCollection);
    }

    /**
     * @param null|QueryContext $context
     * @return PaginatedResult
     */
    public function getUsers(?QueryContext $context = null): PaginatedResult
    {
        $userDTOCollection = [];

        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);
        foreach ($users as $user) {
            $userDTOCollection[] = UserDTO::fromArray($user);
        }

        return UserPaginatedResult::singlePage($userDTOCollection);
    }

}