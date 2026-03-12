<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryStorage;
use TSwiackiewicz\AwesomeApp\ReadModel\User\{
    UserDTO, UserQuery, UserReadModelRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\Query\{
    PaginatedResult, QueryContext, Sort\Sort
};

class InMemoryUserReadModelRepository implements UserReadModelRepository
{
    public function findById(UserId $id): ?UserDTO
    {
        $users = $this->fetchAllUsers();

        return $users[$id->getId()] ?? null;
    }

    private function fetchAllUsers(?Sort $sort = null): array
    {
        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER, $sort);

        return array_map(function (array $user) {
            return UserDTO::fromArray($user);
        }, $users);
    }

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

        return PaginatedResult::withPagination($filteredUsers, $context->getPagination());
    }

    public function getUsers(QueryContext $context): PaginatedResult
    {
        $users = $this->fetchAllUsers($context->getSort());

        return PaginatedResult::withPagination($users, $context->getPagination());
    }
}
