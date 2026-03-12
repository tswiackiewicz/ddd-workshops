<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

class UserRegisteredEvent extends UserEvent
{
    public function __construct(
        UserId $id,
        private readonly string $login,
        private readonly string $password,
        ?\DateTimeImmutable $occurredOn = null
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
