<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Domain\User\Event;

use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;
use TSwiackiewicz\DDD\Event\Event;

abstract readonly class UserEvent implements Event
{
    public function __construct(
        protected UserId $id,
        protected \DateTimeImmutable $occurredOn = new \DateTimeImmutable()
    ) {}

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getOccurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
