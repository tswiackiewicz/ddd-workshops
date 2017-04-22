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
     * @param string $login
     * @return UserDTO
     */
    public function findByLogin(string $login): UserDTO;
}