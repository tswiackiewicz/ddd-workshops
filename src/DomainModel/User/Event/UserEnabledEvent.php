<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\User;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserId;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;

/**
 * Class UserEnabledEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
class UserEnabledEvent extends UserEvent
{
    /**
     * @param User $user
     * @return UserEnabledEvent
     * @throws InvalidArgumentException
     */
    public static function fromUser(User $user): UserEnabledEvent
    {
        return new static(
            UserId::fromInt($user->getId()),
            new UserLogin($user->getLogin())
        );
    }
}