<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserEvent;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class FakeUserEvent
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User\Event
 */
class FakeUserEvent extends UserEvent
{
    /**
     * @return FakeUserEvent
     */
    public static function create(): FakeUserEvent
    {
        /** @var UserId $userId */
        $userId = UserId::generate()->setId(1234);

        return new static($userId);
    }
}