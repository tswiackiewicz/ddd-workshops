<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserActivatedEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserDisabledEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserRegisteredEvent;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\EventSourcing\AggregateHistory;

/**
 * Class UserFactory
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
class UserFactory
{
    /**
     * @param array $user
     * @return User
     */
    public function fromNative(array $user): User
    {
        /** @var UserId $userId */
        $userId = UserId::fromInt($user['id']);

        $events = [
            new UserRegisteredEvent(
                $userId,
                $user['login'],
                $user['password']
            ),
            new UserActivatedEvent($userId)
        ];

        if (false === $user['enabled']) {
            $events[] = new UserDisabledEvent($userId);
        }

        return User::reconstituteFrom(
            new AggregateHistory($userId, $events)
        );
    }
}