<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Domain\User\Event;

use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;

final readonly class UserPasswordChangedEvent extends UserEvent
{
    public function __construct(
        UserId $id,
        private string $password,
        \DateTimeImmutable $occurredOn = new \DateTimeImmutable()
    ) {
        parent::__construct($id, $occurredOn);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function __toString(): string
    {
        return sprintf(
            '[%s] User password changed: id = %d, new password = %s',
            $this->occurredOn->format('Y-m-d H:i:s'),
            $this->id->getId(),
            md5($this->password)
        );
    }
}
