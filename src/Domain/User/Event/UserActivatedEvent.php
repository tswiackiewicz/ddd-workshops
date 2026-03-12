<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Domain\User\Event;

use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserStatus;

final readonly class UserActivatedEvent extends UserEvent
{
    public function getStatus(): UserStatus
    {
        return UserStatus::ACTIVE;
    }

    public function isActive(): bool
    {
        return true;
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function __toString(): string
    {
        return sprintf(
            '[%s] User activated: id = %d',
            $this->occurredOn->format('Y-m-d H:i:s'),
            $this->id->getId()
        );
    }
}
