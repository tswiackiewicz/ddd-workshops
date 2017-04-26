<?php
declare(strict_types = 1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Command;


use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;

/**
 * Class ChangePasswordCommand
 * @package TSwiackiewicz\AwesomeApp\Application\User\Command
 */
class ChangePasswordCommand implements UserCommand
{
    /**
     * @var UserLogin
     */
    private $login;

    /**
     * @var UserPassword
     */
    private $currentPassword;

    /**
     * @var UserPassword
     */
    private $newPassword;

    /**
     * ChangePasswordCommand constructor.
     * @param UserLogin $login
     * @param UserPassword $currentPassword
     * @param UserPassword $newPassword
     */
    public function __construct(UserLogin $login, UserPassword $currentPassword, UserPassword $newPassword)
    {
        $this->login = $login;
        $this->currentPassword = $currentPassword;
        $this->newPassword = $newPassword;
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
    public function getCurrentPassword(): UserPassword
    {
        return $this->currentPassword;
    }

    /**
     * @return UserPassword
     */
    public function getNewPassword(): UserPassword
    {
        return $this->newPassword;
    }
}