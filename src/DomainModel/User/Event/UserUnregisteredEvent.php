<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

/**
 * Class UserUnregisteredEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
class UserUnregisteredEvent extends UserEvent
{
    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return false;
    }
    
    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '[%s] User unregistered: id = %d',
            $this->occurredOn->format('Y-m-d H:i:s'),
            $this->id->getId()
        );
    }
}