<?php

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\ReadModel\User\UserDTO;
use TSwiackiewicz\AwesomeApp\ReadModel\User\UserReadModelRepository;

/**
 * Class InMemoryUserReadModelRepository
 * @package TSwiackiewicz\AwesomeApp\Infrastructure\User
 */
class InMemoryUserReadModelRepository implements UserReadModelRepository
{
    /**
     * @param string $login
     * @return UserDTO
     */
    public function findByLogin(string $login): UserDTO
    {
        // TODO: Implement findByLogin() method.
    }
}