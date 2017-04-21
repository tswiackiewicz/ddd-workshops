<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

/**
 * Class User
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

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password->getPassword();
    }
}