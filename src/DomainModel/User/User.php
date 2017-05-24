<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Event\UserEvent;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\RuntimeException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Abstract core for multiple User's BC
 *
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
abstract class User
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
     * @var UserPassword
     */
    protected $password;

    /**
     * User constructor.
     * @param UserId $id
     * @param UserLogin $login
     * @param UserPassword $password
     */
    public function __construct(UserId $id, UserLogin $login, UserPassword $password)
    {
        $this->id = $id;
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * @return UserId
     */
    public function getId(): UserId
    {
        return $this->id;
    }

    /**
     * @return UserLogin
     */
    public function getLogin(): UserLogin
    {
        return $this->login;
    }

    /**
     * @return UserPassword
     */
    public function getPassword(): UserPassword
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function hash(): string
    {
        $hash = md5($this->login . '::' . $this->password);

        // salt added to User's hash
        return substr($hash, 0, 8) . substr($hash, 24, 8) .
            substr($hash, 16, 8) . substr($hash, 8, 8);
    }

    /**
     * @param UserEvent $event
     */
    protected function apply(UserEvent $event): void
    {
        $method = 'apply' . get_class($event);
        if (!method_exists($this, $method)) {
            throw new RuntimeException();
        }

        $this->$method($event);
    }
}