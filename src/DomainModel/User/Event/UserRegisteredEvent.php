<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    User, UserLogin
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class UserRegisteredEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
class UserRegisteredEvent extends UserEvent
{
    /**
     * @param User $user
     * @return UserRegisteredEvent
     * @throws InvalidArgumentException
     */
    public static function fromUser(User $user): UserRegisteredEvent
    {
        return new static(
            UserId::fromInt($user->getId()),
            new UserLogin($user->getLogin())
        );
    }
}