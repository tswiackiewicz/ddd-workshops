<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

class UserUnregisteredEvent extends UserEvent
{
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
