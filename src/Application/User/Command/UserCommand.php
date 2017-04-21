<?php
declare(strict_types = 1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Command;

use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;

/**
 * Interface UserCommand
 * @package TSwiackiewicz\AwesomeApp\Application\User\Command
 */
interface UserCommand
{
    /**
     * @return UserLogin
     */
    public function getLogin(): UserLogin;
}