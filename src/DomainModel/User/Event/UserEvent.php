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
     * @return \DateTimeImmutable
     */
    public function getOccurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }

    /**
     * @return string
     */
    public function serialize(): string
    {
        return json_encode(
            array_merge(
                [
                    'id' => $this->id->getId(),
                    'occurred_on' => $this->occurredOn
                ],
                $this->doSerialize()
            )
        );
    }
    
    /**
     * @return array
     */
    protected function doSerialize(): array
    {
        return [];
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        $deserializedObject = json_decode($serialized, true);

        $this->id = UserId::fromInt($deserializedObject['id']);
        $this->occurredOn = new \DateTimeImmutable(
            $deserializedObject['occurred_on']['date'],
            new \DateTimeZone($deserializedObject['occurred_on']['timezone'])
        );

        $this->doUnserialize($deserializedObject);
    }

    /**
     * @param array $unserializedObject
     */
    protected function doUnserialize(array $unserializedObject): void
    {
        // noop
    }

    /**
     * @return UserId
     */
    public function getId(): UserId
    {
        return $this->id;
    }
}