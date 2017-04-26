<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class UserEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
abstract class UserEvent
{
    /**
     * @var UserId
     */
    protected $id;

    /**
     * @var string
     */
    protected $login;

    /**
     * UserRemovedEvent constructor.
     * @param UserId $id
     * @param string $login
     */
    public function __construct(UserId $id, string $login)
    {
        $this->id = $id;
        $this->login = $login;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id->getId();
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }
}