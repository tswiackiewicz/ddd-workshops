<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Command;

use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserLogin;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserPassword;

class RegisterUserCommand implements UserCommand
{
    public function __construct(
        private readonly UserLogin $login,
        private readonly UserPassword $password
    ) {
    }

    public function getLogin(): UserLogin
    {
        return $this->login;
    }

    public function getPassword(): UserPassword
    {
        return $this->password;
    }
}
