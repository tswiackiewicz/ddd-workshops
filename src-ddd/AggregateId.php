<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class AggregateId
 * @package TSwiackiewicz\DDD
 */
class AggregateId
{
    /**
     * @var UuidInterface
     */
    protected $uuid;

    /**
     * @var int
     */
    protected $id = 0;

    /**
     * AggregateId constructor.
     * @param UuidInterface $uuid
     */
    protected function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @param string $uuid
     * @return AggregateId
     */
    public static function fromString(string $uuid): AggregateId
    {
        return new static(Uuid::fromString($uuid));
    }

    /**
     * @return AggregateId
     */
    public static function generate(): AggregateId
    {
        return new static(Uuid::uuid4());
    }

    /**
     * @return string
     */
    public function getAggregateId(): string
    {
        return $this->uuid->toString();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return AggregateId
     * @throws InvalidArgumentException
     */
    public function setId(int $id): AggregateId
    {
        if ($this->id > 0) {
            return $this;
        }

        if ($id <= 0) {
            throw new InvalidArgumentException('AggregateId is not allowed to be below zero');
        }

        $aggregateId = clone $this;
        $aggregateId->id = $id;

        return $aggregateId;
    }

    /**
     * @param AggregateId $aggregateId
     * @return bool
     */
    public function equals(AggregateId $aggregateId): bool
    {
        return $aggregateId->getAggregateId() === $this->getAggregateId() && $aggregateId->getId() === $this->getId();
    }
}