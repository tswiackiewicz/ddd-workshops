<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Domain\User\ValueObject;

use TSwiackiewicz\AwesomeApp\SharedKernel\Exception\InvalidArgumentException;

final readonly class UserLogin
{
    private string $login;

    public function __construct(string $login)
    {
        if (false === filter_var($login, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Login should be a valid email address');
        }

        $this->login = $login;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function __toString(): string
    {
        return $this->login;
    }
}
