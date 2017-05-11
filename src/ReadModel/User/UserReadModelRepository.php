<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\ReadModel\User;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\Query\PaginatedResult;
use TSwiackiewicz\DDD\Query\QueryContext;

/**
 * Interface UserReadModelRepository
 * @package TSwiackiewicz\AwesomeApp\ReadModel\User
 */
interface UserReadModelRepository
{
    /**
     * @param UserId $id
     * @return null|UserDTO
     */
    public function findById(UserId $id): ?UserDTO;

    /**
     * @param UserQuery $query
     * @param null|QueryContext $context
     * @return PaginatedResult
     */
    public function findByQuery(UserQuery $query, ?QueryContext $context = null): PaginatedResult;

    /**
     * @param null|QueryContext $context
     * @return PaginatedResult
     */
    public function getUsers(?QueryContext $context = null): PaginatedResult;
}