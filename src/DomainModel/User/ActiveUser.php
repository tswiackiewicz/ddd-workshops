<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Example of two different User's Bounded Contexts
 * It can be organized within same or various (sub-)namespaces
 *
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User
 */
class ActiveUser extends User
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * ActiveUser constructor.
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

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return true;
    }
}
