<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

/**
 * Class UserActivatedEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
class UserActivatedEvent extends UserEvent
{
    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '[%s] User activated: id = %d',
            $this->occurredOn->format('Y-m-d H:i:s'),
            $this->id
        );
    }
}