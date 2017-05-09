<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class UserRegisteredEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
class UserRegisteredEvent extends UserEvent
{
    /**
     * @var string
     */
    private $login;

    /**
     * UserRegisteredEvent constructor.
     * @param UserId $id
     * @param string $login
     * @param \DateTimeImmutable|null $occurredOn
     */
    public function __construct(UserId $id, string $login, ?\DateTimeImmutable $occurredOn = null)
    {
        parent::__construct($id, $occurredOn);
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '[%s] User registered: id = %d, login = %s',
            $this->occurredOn->format('Y-m-d H:i:s'),
            $this->id->getId(),
            $this->login
        );
    }
}