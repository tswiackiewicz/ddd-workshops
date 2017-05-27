<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD;

use InvalidArgumentException;
use RuntimeException;

/**
 * Class AggregateId
 * @package TSwiackiewicz\DDD
 */
abstract class AggregateId
{
    protected const NULL_ID = 0;

    /**
     * @var int
     */
    protected $id;

    /**
     * AggregateId constructor.
     * @param int $id
     */
    protected function __construct(int $id)
    {
        if ($id < 0) {
            throw new InvalidArgumentException('AggregateId is not allowed to be below zero');
        }

        $this->id = $id;
    }

    /**
     * @param int $id
     * @return AggregateId
     * @throws InvalidArgumentException
     */
    public static function fromInt(int $id): AggregateId
    {
        return new static($id);
    }

    /**
     * @return AggregateId
     */
    public static function nullInstance(): AggregateId
    {
        try {
            return new static(static::NULL_ID);
        } catch (InvalidArgumentException $exception) {
            // we need nullInstance method interface to be clear (without thrown exceptions),
            // object construction contract declares InvalidArgumentException to be thrown
            // on the other hand, it is impossible to throw InvalidArgument exception when
            // we construct object with self::NULL_ID identity,
            // Runtime exceptions could be treated as Java language unchecked exceptions,
            // so we do not need to declare them
            throw new RuntimeException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @return bool
     */
    public function isNull(): bool
    {
        return static::NULL_ID === $this->id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function __toString()
    {
        return (string)$this->id;
    }
}