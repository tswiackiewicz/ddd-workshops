<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

class UserDisabledEvent extends UserEvent
{
    public function isEnabled(): bool
    {
        return false;
    }

    public function __toString(): string
    {
        return sprintf(
            '[%s] User disabled: id = %d',
            $this->occurredOn->format('Y-m-d H:i:s'),
            $this->id->getId()
        );
    }
}
