<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\SharedKernel\User;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\DDD\AggregateId;

/**
 * Class UserId
 * @package TSwiackiewicz\AwesomeApp\SharedKernel\User
 */
class UserId extends AggregateId
{
    /**
     * @param AggregateId $aggregateId
     * @return UserId|AggregateId
     */
    public static function fromAggregateId(AggregateId $aggregateId): UserId
    {
        return static::fromString($aggregateId->getAggregateId())->setId($aggregateId->getId());
    }

    /**
     * @param int $id
     * @return AggregateId
     * @throws InvalidArgumentException
     */
    public function setId(int $id): AggregateId
    {
        try {
            return parent::setId($id);
        } catch (\InvalidArgumentException $exception) {
            throw new InvalidArgumentException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }
}