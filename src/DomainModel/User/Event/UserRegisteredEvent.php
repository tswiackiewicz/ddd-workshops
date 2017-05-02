<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

/**
 * Class UserRegisteredEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
class UserRegisteredEvent extends UserEvent
{
    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('User registered: id = %d, login = %s', $this->id->getId(), $this->login);
    }
}