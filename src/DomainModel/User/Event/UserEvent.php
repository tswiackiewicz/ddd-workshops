<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\Event\Event;

abstract class UserEvent implements Event
{
    protected readonly UserId $id;
    protected readonly \DateTimeImmutable $occurredOn;

    public function __construct(UserId $id, ?\DateTimeImmutable $occurredOn = null)
    {
        $this->id = $id;
        $this->occurredOn = $occurredOn ?? new \DateTimeImmutable();
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getOccurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
