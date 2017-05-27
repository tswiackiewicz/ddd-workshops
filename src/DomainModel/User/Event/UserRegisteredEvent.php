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
     * @var string
     */
    private $password;

    /**
     * UserRegisteredEvent constructor.
     * @param UserId $id
     * @param string $login
     * @param string $password
     * @param \DateTimeImmutable|null $occurredOn
     */
    public function __construct(UserId $id, string $login, string $password, ?\DateTimeImmutable $occurredOn = null)
    {
        parent::__construct($id, $occurredOn);
        $this->login = $login;
        $this->password = $password;
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
            '[%s] User registered: id = %d, login = %s, password = %s',
            $this->occurredOn->format('Y-m-d H:i:s'),
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
        return [
            'login' => $this->login,
            'password' => $this->password
        ];
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