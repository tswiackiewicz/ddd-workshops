<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\InvalidArgumentException;

/**
 * Class UserPassword
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
class UserPassword
{
    private const MIN_PASSWORD_LENGTH = 8;

    /**
     * @var string
     */
    private $password;

    /**
     * UserPassword constructor.
     * @param string $password
     * @throws InvalidArgumentException
     */
    public function __construct(string $password)
    {
        $this->assertPassword($password);

        $this->password = $password;
    }

    /**
     * @param string $password
     * @throws InvalidArgumentException
     */
    private function assertPassword(string $password): void
    {
        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            throw new InvalidArgumentException('');
        }

        // TODO: other password assert rules
    }

    /**
     * @return UserPassword
     */
    public static function generate(): UserPassword
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new static(uniqid('', false));
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
        return $this->password;
    }
}