<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Password;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;

class UserPassword
{
    private const MIN_PASSWORD_LENGTH = 8;

    private readonly string $password;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $password)
    {
        $this->assertPassword($password);

        $this->password = $password;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function assertPassword(string $password): void
    {
        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            throw new InvalidArgumentException('');
        }

        // TODO: other password assertion rules
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function equals(UserPassword $password): bool
    {
        return $this->password === (string)$password;
    }

    public function __toString(): string
    {
        return $this->password;
    }
}
