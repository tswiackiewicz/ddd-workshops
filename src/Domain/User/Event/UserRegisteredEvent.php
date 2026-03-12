<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Domain\User\Event;

use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;

final readonly class UserRegisteredEvent extends UserEvent
{
    public function __construct(
        UserId $id,
        private string $login,
        private string $password,
        \DateTimeImmutable $occurredOn = new \DateTimeImmutable()
    ) {
        parent::__construct($id, $occurredOn);
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function __toString(): string
    {
        return sprintf(
            '[%s] User registered: id = %d, login = %s, password = %s',
            $this->occurredOn->format('Y-m-d H:i:s'),
            $this->id->getId(),
            $this->login,
            md5($this->password)
        );
    }
}
