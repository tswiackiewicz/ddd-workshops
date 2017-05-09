<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\Event\Event;

/**
 * Class UserEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
abstract class UserEvent implements Event
{
    /**
     * @var UserId
     */
    protected $id;

    /**
     * @var \DateTimeImmutable
     */
    protected $occurredOn;

    /**
     * UserEvent constructor.
     * @param UserId $id
     * @param null|\DateTimeImmutable $occurredOn
     */
    public function __construct(UserId $id, ?\DateTimeImmutable $occurredOn = null)
    {
        $this->id = $id;
        $this->occurredOn = $occurredOn ?: new \DateTimeImmutable();
    }

    /**
     * @return UserId
     */
    public function getId(): UserId
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getOccurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}