<?php
declare(strict_types = 1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Command;

use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;

/**
 * Class ActivateUserCommand
 * @package TSwiackiewicz\AwesomeApp\Application\User\Command
 */
class ActivateUserCommand implements UserCommand
{
    /**
     * @var UserLogin
     */
    private $login;

    /**
     * @var string
     */
    private $hash;

    /**
     * ActivateUserCommand constructor.
     * @param UserLogin $login
     * @param string $hash
     */
    public function __construct(UserLogin $login, string $hash)
    {
        $this->login = $login;
        $this->hash = $hash;
    }

    /**
     * @return UserLogin
     */
    public function getLogin(): UserLogin
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }
}