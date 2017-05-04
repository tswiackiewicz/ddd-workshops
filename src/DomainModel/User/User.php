<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
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
        return md5($this->login . '::' . $this->password);
    }

    /**
     * @return bool
     */
    abstract public function isEnabled(): bool;

    /**
     * @return bool
     */
    abstract public function isActive(): bool;
}
