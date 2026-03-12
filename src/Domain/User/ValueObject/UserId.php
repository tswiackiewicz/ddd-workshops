<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Domain\User\ValueObject;

use TSwiackiewicz\AwesomeApp\SharedKernel\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\Exception\RuntimeException;

final readonly class UserId
{
    private const NULL_ID = 0;

    private function __construct(private int $id)
    {
        if ($id < 0) {
            throw new InvalidArgumentException('Not allowed ');
        }
    }

    public static function fromInt(int $id): UserId
    {
        return new self($id);
    }

    public static function nullInstance(): UserId
    {
        try {
            return new self(self::NULL_ID);
        } catch (InvalidArgumentException $exception) {
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
