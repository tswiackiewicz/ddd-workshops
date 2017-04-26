<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\ReadModel\User;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

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
     * @return UserDTO[]
     */
    public function findByQuery(UserQuery $query): array;

    /**
     * @return UserDTO[]
     */
    public function getAllUsers(): array;
}