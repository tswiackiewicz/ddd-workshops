<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\ReadModel\User;

/**
 * Interface UserReadModelRepository
 * @package TSwiackiewicz\AwesomeApp\ReadModel\User
 */
interface UserReadModelRepository
{
    /**
     * @param int $id
     * @return null|UserDTO
     */
    public function findById(int $id): ?UserDTO;

    /**
     * @param UserQuery $query
     * @return UserDTO[]
     */
    public function findByQuery(UserQuery $query): array;

    /**
     * @return UserDTO[]
     */
    public function getAllUsers() : array;
}