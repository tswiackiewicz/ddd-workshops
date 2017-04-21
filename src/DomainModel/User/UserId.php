<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\InvalidArgumentException;

/**
 * Class UserId
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
class UserId
{
    private const NULL_ID = 0;

    /**
     * @var int
     */
    private $id;

    /**
     * UserId constructor.
     * @param int $id
     * @throws InvalidArgumentException
     */
    private function __construct(int $id)
    {
        if ($id < 0) {
            throw new InvalidArgumentException('Not allowed ');
        }

        $this->id = $id;
    }

    /**
     * @param int $id
     * @return UserId
     * @throws InvalidArgumentException
     */
    public static function fromInt(int $id): UserId
    {
        return new self($id);
    }

    /**
     * @return UserId
     */
    public static function nullInstance(): UserId
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new self(self::NULL_ID);
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
}