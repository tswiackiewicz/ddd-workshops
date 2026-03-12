<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Command;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

class ChangePasswordCommand implements UserCommand
{
    public function __construct(
        private readonly UserId $userId,
        private readonly UserPassword $password
    ) {
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getPassword(): UserPassword
    {
        return $this->password;
    }
}
