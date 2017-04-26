<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class LoggedInUser
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
class LoggedInUser extends User
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * LoggedInUser constructor.
     * @param UserId $id
     * @param UserLogin $login
     * @param UserPassword $password
     * @param bool $enabled
     */
    public function __construct(UserId $id, UserLogin $login, UserPassword $password, bool $enabled)
    {
        parent::__construct($id, $login, $password);
        $this->enabled = $enabled;
    }

    /**
     * Enable user
     */
    public function enable(): void
    {
        $this->enabled = true;
    }

    /**
     * Disable user
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * @param UserPassword $password
     */
    public function changePassword(UserPassword $password): void
    {
        $this->password = $password;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}