<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Query;

final readonly class UserDTO
{
    public function __construct(
        private int $id,
        private string $login,
        private string $password,
        private bool $active,
        private bool $enabled
    ) {
    }

    public static function fromArray(array $user): UserDTO
    {
        return new static(
            $user['id'],
            $user['login'],
            $user['password'],
            $user['active'] ?? false,
            $user['enabled'] ?? false
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
