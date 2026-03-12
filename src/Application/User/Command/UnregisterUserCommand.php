<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Command;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

class UnregisterUserCommand implements UserCommand
{
    public function __construct(private readonly UserId $userId)
    {
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }
}
