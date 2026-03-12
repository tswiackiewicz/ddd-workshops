<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\SharedKernel\User;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\RuntimeException;

class UserId
{
    private const NULL_ID = 0;

    private function __construct(private readonly int $id)
    {
        if ($id < 0) {
            throw new InvalidArgumentException('Not allowed ');
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function fromInt(int $id): UserId
    {
        return new self($id);
    }

    public static function nullInstance(): UserId
    {
        try {
            return new self(self::NULL_ID);
        } catch (InvalidArgumentException $exception) {
            // we need withoutSort method interface to be clear (without thrown exceptions)
            // but object construction contract declares InvalidArgumentException to be thrown
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

    public function isNull(): bool
    {
        return static::NULL_ID === $this->id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
