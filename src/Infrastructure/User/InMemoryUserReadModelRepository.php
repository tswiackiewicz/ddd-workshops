<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryStorage;
use TSwiackiewicz\AwesomeApp\ReadModel\User\UserDTO;
use TSwiackiewicz\AwesomeApp\ReadModel\User\UserQuery;
use TSwiackiewicz\AwesomeApp\ReadModel\User\UserReadModelRepository;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\Query\PaginatedResult;
use TSwiackiewicz\DDD\Query\QueryContext;
use TSwiackiewicz\DDD\Query\Sort;

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
        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);

        $filteredUsers = [];
        foreach ($users as $user) {
            if (isset($user['active'], $user['enabled']) &&
                $query->isActive() === $user['active'] &&
                $query->isEnabled() === $user['enabled']
            ) {
                $filteredUsers[] = $user;
            }
        }

        return $this->buildPaginatedResult($filteredUsers, $context ?: new QueryContext());
    }

    /**
     * @param array $users
     * @param QueryContext $context
     * @return PaginatedResult
     */
    private function buildPaginatedResult(array $users, QueryContext $context): PaginatedResult
    {
        $sortedUsers = $this->sortUsers($users, $context->getSort());

        if (null === $context->getPagination()->getPerPage()) {
            return PaginatedResult::singlePage($sortedUsers);
        }

        $paginatedUsers = array_slice(
            $sortedUsers,
            $context->getPagination()->getOffset(),
            $context->getPagination()->getPerPage()
        );

        return new PaginatedResult(
            $paginatedUsers,
            $context->getPagination()->getCurrentPage(),
            $context->getPagination()->getPerPage(),
            count($users)
        );
    }

    /**
     * @param array $users
     * @param Sort $sort
     * @return UserDTO[]
     */
    private function sortUsers(array $users, Sort $sort): array
    {
        $sortedUsers = $users;
        if ($sort->getFieldName() !== '') {
            usort($sortedUsers, function ($a, $b) use ($sort) {
                if (is_numeric($a[$sort->getFieldName()])) {
                    $diff = $a[$sort->getFieldName()] - $b[$sort->getFieldName()];
                } else {
                    $diff = strcmp($a[$sort->getFieldName()], $b[$sort->getFieldName()]);
                }

                return $diff * ($sort->isAscendingOrder() ? 1 : -1);
            });
        }

        return array_map(function (array $user) {
            return UserDTO::fromArray($user);
        }, $sortedUsers);
    }

    /**
     * @param null|QueryContext $context
     * @return PaginatedResult
     */
    public function getUsers(?QueryContext $context = null): PaginatedResult
    {
        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);

        return $this->buildPaginatedResult($users, $context ?: new QueryContext());
    }
}