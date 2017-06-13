<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\AggregateId;

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
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $hash;

    /**
     * UserRegisteredEvent constructor.
     * @param AggregateId $id
     * @param string $login
     * @param string $password
     * @param string $hash
     * @param \DateTimeImmutable|null $occurredOn
     */
    public function __construct(
        AggregateId $id,
        string $login,
        string $password,
        string $hash,
        ?\DateTimeImmutable $occurredOn = null
    )
    {
        parent::__construct($id, $occurredOn);
        $this->login = $login;
        $this->password = $password;
        $this->hash = $hash;
    }

    /**
     * @param UserId $userId
     * @return UserRegisteredEvent
     */
    public function withUserId(UserId $userId): UserRegisteredEvent
    {
        $event = clone $this;
        $event->id = $userId;
        return $event;
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
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '[%s] User registered: uuid = %s, id = %d, login = %s, password = %s',
            $this->occurredOn->format('Y-m-d H:i:s'),
            $this->id->getAggregateId(),
            $this->id->getId(),
            $this->login,
            md5($this->password)
        );
    }

    /**
     * @return array
     */
    protected function doSerialize(): array
    {
        return array_merge(
            parent::doSerialize(),
            [
                'login' => $this->login,
                'password' => $this->password
            ]
        );
    }

    /**
     * @param array $unserialized
     */
    protected function doUnserialize(array $unserialized): void
    {
        $this->login = $unserialized['login'];
        $this->password = $unserialized['password'];
    }
}