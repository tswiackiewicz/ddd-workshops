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
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('User unregistered: id = %d, login = %s', $this->id->getId(), $this->login);
    }
}