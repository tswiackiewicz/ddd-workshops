<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Domain\User\Event;

use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserStatus;

final readonly class UserUnregisteredEvent extends UserEvent
{
    public function getStatus(): UserStatus
    {
        return UserStatus::INACTIVE;
    }

    public function isActive(): bool
    {
        return false;
    }

    public function isEnabled(): bool
    {
        return false;
    }

    public function __toString(): string
    {
        return sprintf(
            '[%s] User unregistered: id = %d',
            $this->occurredOn->format('Y-m-d H:i:s'),
            $this->id->getId()
        );
    }
}
