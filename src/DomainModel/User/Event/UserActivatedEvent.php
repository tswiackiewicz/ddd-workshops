<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Event;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\User;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserId;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;

/**
 * Class UserActivatedEvent
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Event
 */
class UserActivatedEvent extends UserEvent
{
    /**
     * @var string
     */
    private $hash;

    /**
     * UserActivatedEvent constructor.
     * @param UserId $id
     * @param UserLogin $login
     * @param string $hash
     */
    public function __construct(UserId $id, UserLogin $login, string $hash)
    {
        parent::__construct($id, $login);
        $this->hash = $hash;
    }

    /**
     * @param User $user
     * @param string $hash
     * @return UserActivatedEvent
     * @throws InvalidArgumentException
     */
    public static function fromUser(User $user, string $hash): UserActivatedEvent
    {
        return new static(
            UserId::fromInt($user->getId()),
            new UserLogin($user->getLogin()),
            $hash
        );
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }
}