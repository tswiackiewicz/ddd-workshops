<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Domain\User\ValueObject;

use TSwiackiewicz\AwesomeApp\SharedKernel\Exception\InvalidArgumentException;

final readonly class UserPassword
{
    private const MIN_PASSWORD_LENGTH = 8;

    private string $password;

    public function __construct(string $password)
    {
        $this->assertPassword($password);

        $this->password = $password;
    }

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
