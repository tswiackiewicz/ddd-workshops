<?php
declare(strict_types = 1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Command;

use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserPassword;

/**
 * Class RegisterUserCommand
 * @package TSwiackiewicz\AwesomeApp\Application\User\Command
 */
class RegisterUserCommand implements UserCommand
{
    /**
     * @var UserLogin
     */
    private $login;

    /**
     * @var UserPassword
     */
    private $password;

    /**
     * RegisterUserCommand constructor.
     * @param UserLogin $login
     * @param UserPassword $password
     */
    public function __construct(UserLogin $login, UserPassword $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * @return UserLogin
     */
    public function getLogin(): UserLogin
    {
        return $this->login;
    }

    /**
     * @return UserPassword
     */
    public function getPassword(): UserPassword
    {
        return $this->password;
    }
}