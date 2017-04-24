<?php

namespace TSwiackiewicz\AwesomeApp\ReadModel\User;

/**
 * Class UserDTO
 * @package TSwiackiewicz\AwesomeApp\ReadModel\User
 */
class UserDTO
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * UserDTO constructor.
     * @param int $id
     * @param string $login
     * @param string $password
     * @param bool $active
     * @param bool $enabled
     */
    public function __construct($id, $login, $password, $active, $enabled)
    {
        $this->id = $id;
        $this->login = $login;
        $this->password = $password;
        $this->active = $active;
        $this->enabled = $enabled;
    }

    /**
     * @param array $user
     * @return UserDTO
     */
    public static function fromArray(array $user) : UserDTO
    {
        return new static(
            $user['id'],
            $user['login'],
            $user['password'],
            $user['active'],
            $user['enabled']
        );
    }

    /**
     * @return int
     */
    public function getId(): int
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
        return $this->active;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}