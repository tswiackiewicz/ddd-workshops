<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

use TSwiackiewicz\DDD\AggregateId;

/**
 * Class UserPasswordChangedEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
class UserPasswordChangedEvent extends UserEvent
{
    /**
     * @var string
     */
    private $password;

    /**
     * UserPasswordChangedEvent constructor.
     * @param AggregateId $id
     * @param string $password
     * @param \DateTimeImmutable|null $occurredOn
     */
    public function __construct(AggregateId $id, string $password, ?\DateTimeImmutable $occurredOn = null)
    {
        parent::__construct($id, $occurredOn);
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '[%s] User password changed: uuid = %s, id = %d, new password = %s',
            $this->occurredOn->format('Y-m-d H:i:s'),
            $this->id->getAggregateId(),
            $this->id->getId(),
            md5($this->password)
        );
    }
}