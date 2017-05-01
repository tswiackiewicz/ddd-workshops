<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Command;

use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;

/**
 * Class ResetPasswordCommand
 * @package TSwiackiewicz\AwesomeApp\Application\User\Command
 */
class ResetPasswordCommand implements UserCommand
{
    /**
     * @var UserLogin
     */
    private $login;

    /**
     * @var string
     */
    private $token;

    /**
     * ResetPasswordCommand constructor.
     * @param UserLogin $login
     * @param string $token
     */
    public function __construct(UserLogin $login, string $token)
    {
        $this->login = $login;
        $this->token = $token;
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
    public function getToken(): string
    {
        return $this->token;
    }
}