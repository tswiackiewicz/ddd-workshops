<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\SharedKernel\User;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\RuntimeException;

/**
 * Class UserId
 * @package TSwiackiewicz\AwesomeApp\SharedKernel\User
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