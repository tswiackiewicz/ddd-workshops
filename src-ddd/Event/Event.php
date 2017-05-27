<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\Event;

use TSwiackiewicz\DDD\AggregateId;

/**
 * Class Event
 * @package TSwiackiewicz\DDD\Event
 */
abstract class Event implements \Serializable
{
    protected const SERIALIZED_EVENT_AGGREGATE_ID = 'id';
    protected const SERIALIZED_EVENT_OCCURRED_ON = 'occurred_on';

    /**
     * @var AggregateId
     */
    protected $id;

    /**
     * @var \DateTimeImmutable
     */
    protected $occurredOn;

    /**
     * Event constructor.
     * @param AggregateId $id
     * @param \DateTimeImmutable|null $occurredOn
     */
    public function __construct(AggregateId $id, ?\DateTimeImmutable $occurredOn = null)
    {
        $this->id = $id;
        $this->occurredOn = $occurredOn ?: new \DateTimeImmutable();
    }

    /**
     * @return AggregateId
     */
    public function getId(): AggregateId
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

    /**
     * @return string
     */
    public function serialize(): string
    {
        return json_encode(
            array_merge(
                [
                    self::SERIALIZED_EVENT_AGGREGATE_ID => $this->id->getId(),
                    self::SERIALIZED_EVENT_OCCURRED_ON => $this->occurredOn
                ],
                $this->doSerialize()
            )
        );
    }

    /**
     * This method can be overridden - custom event properties serialization
     *
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
        $unserialized = json_decode($serialized, true);

        $this->id = $this->doUnserializeId($unserialized[self::SERIALIZED_EVENT_AGGREGATE_ID]);
        $this->occurredOn = new \DateTimeImmutable(
            $unserialized[self::SERIALIZED_EVENT_OCCURRED_ON]['date'],
            new \DateTimeZone($unserialized[self::SERIALIZED_EVENT_OCCURRED_ON]['timezone'])
        );

        $this->doUnserialize($unserialized);
    }

    /**
     * @param int $id
     * @return AggregateId
     */
    abstract protected function doUnserializeId(int $id): AggregateId;

    /**
     * This method can be overridden - custom event properties deserialization
     *
     * @param array $unserialized
     */
    protected function doUnserialize(array $unserialized): void
    {
        // noop
    }
}