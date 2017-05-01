<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Command;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class ChangePasswordCommand
 * @package TSwiackiewicz\AwesomeApp\Application\User\Command
 */
class ChangePasswordCommand implements UserCommand
{
    /**
     * @var UserId
     */
    private $userId;

    /**
     * @var UserPassword
     */
    private $currentPassword;

    /**
     * @var UserPassword
     */
    private $newPassword;

    /**
     * ChangePasswordCommand constructor.
     * @param UserId $userId
     * @param UserPassword $currentPassword
     * @param UserPassword $newPassword
     */
    public function __construct(
        UserId $userId,
        UserPassword $currentPassword,
        UserPassword $newPassword
    )
    {
        $this->userId = $userId;
        $this->currentPassword = $currentPassword;
        $this->newPassword = $newPassword;
    }

    /**
     * @return UserId
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return UserPassword
     */
    public function getCurrentPassword(): UserPassword
    {
        return $this->currentPassword;
    }

    /**
     * @return UserPassword
     */
    public function getNewPassword(): UserPassword
    {
        return $this->newPassword;
    }
}