<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\ReadModel\User;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\Query\PaginatedResult;
use TSwiackiewicz\DDD\Query\QueryContext;

/**
 * Class UserReadModel
 * @package TSwiackiewicz\AwesomeApp\ReadModel\User
 */
class UserReadModel
{
    /**
     * @var UserReadModelRepository
     */
    private $repository;

    /**
     * UserReadModel constructor.
     * @param UserReadModelRepository $repository
     */
    public function __construct(UserReadModelRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param UserId $id
     * @return null|UserDTO
     */
    public function findById(UserId $id): ?UserDTO
    {
        return $this->repository->findById($id);
    }

    /**
     * @param UserQuery $query
     * @param null|QueryContext $context
     * @return PaginatedResult
     */
    public function findByQuery(UserQuery $query, ?QueryContext $context = null): PaginatedResult
    {
        return $this->repository->findByQuery($query, $context ?: new QueryContext());
    }

    /**
     * @param null|QueryContext $context
     * @return PaginatedResult
     */
    public function getUsers(?QueryContext $context = null): PaginatedResult
    {
        return $this->repository->getUsers($context ?: new QueryContext());
    }
}