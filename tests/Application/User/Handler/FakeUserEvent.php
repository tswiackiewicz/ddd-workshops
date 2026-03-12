<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Application\User\Handler;

use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;

readonly class FakeUserEvent extends UserEvent
{
    public static function create(): FakeUserEvent
    {
        return new static(
            UserId::fromInt(1234)
        );
    }
}
