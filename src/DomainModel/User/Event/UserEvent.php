<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

use TSwiackiewicz\AwesomeApp\SharedKernel\Event\Event;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class UserEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
abstract class UserEvent implements Event
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
     * @return UserId
     */
    public function getId(): UserId
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }
}