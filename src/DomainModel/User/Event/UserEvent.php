<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
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
     * @var UserLogin
     */
    protected $login;

    /**
     * UserRemovedEvent constructor.
     * @param UserId $id
     * @param UserLogin $login
     */
    public function __construct(UserId $id, UserLogin $login)
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
        return $this->login->getLogin();
    }
}