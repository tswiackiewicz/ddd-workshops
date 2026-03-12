<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Query;

use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;
use TSwiackiewicz\DDD\Query\PaginatedResult;
use TSwiackiewicz\DDD\Query\QueryContext;

interface UserReadModelRepository
{
    public function findById(UserId $id): ?UserDTO;

    public function findByQuery(UserQuery $query, QueryContext $context): PaginatedResult;

    public function getUsers(QueryContext $context): PaginatedResult;
}
