<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryStorage;
use TSwiackiewicz\AwesomeApp\ReadModel\User\{
    UserDTO, UserQuery, UserReadModelRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\Query\{
    PaginatedResult, QueryContext, Sort
};

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
        $users = $this->fetchAllUsers();

        return $users[$id->getId()] ?? null;
    }

    /**
     * @param null|Sort $sort
     * @return array
     */
    private function fetchAllUsers(?Sort $sort = null): array
    {
        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER, $sort ?: Sort::withoutSort());

        return array_map(function (array $user) {
            return UserDTO::fromArray($user);
        }, $users);
    }

    /**
     * @param UserQuery $query
     * @param QueryContext $context
     * @return PaginatedResult
     */
    public function findByQuery(UserQuery $query, QueryContext $context): PaginatedResult
    {
        $users = $this->fetchAllUsers($context->getSort());

        $filteredUsers = [];
        foreach ($users as $userId => $user) {
            /** @var UserDTO $user */
            if ($query->isActive() === $user->isActive() && $query->isEnabled() === $user->isEnabled()) {
                $filteredUsers[] = $user;
            }
        }

        return PaginatedResult::withContext($filteredUsers, $context);
    }

    /**
     * @param QueryContext $context
     * @return PaginatedResult
     */
    public function getUsers(QueryContext $context): PaginatedResult
    {
        $users = $this->fetchAllUsers($context->getSort());

        return PaginatedResult::withContext($users, $context);
    }
}