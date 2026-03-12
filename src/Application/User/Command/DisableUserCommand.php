<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Command;

use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;

class DisableUserCommand implements UserCommand
{
    public function __construct(private readonly UserId $userId)
    {
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }
}
