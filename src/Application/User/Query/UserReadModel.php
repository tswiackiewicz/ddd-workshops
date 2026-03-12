<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Query;

use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;
use TSwiackiewicz\DDD\Query\PaginatedResult;
use TSwiackiewicz\DDD\Query\QueryContext;

class UserReadModel
{
    public function __construct(private readonly UserReadModelRepository $repository)
    {
    }

    public function findById(UserId $id): ?UserDTO
    {
        return $this->repository->findById($id);
    }

    public function findByQuery(UserQuery $query, ?QueryContext $context = null): PaginatedResult
    {
        return $this->repository->findByQuery($query, $context ?: new QueryContext());
    }

    public function getUsers(?QueryContext $context = null): PaginatedResult
    {
        return $this->repository->getUsers($context ?: new QueryContext());
    }
}
