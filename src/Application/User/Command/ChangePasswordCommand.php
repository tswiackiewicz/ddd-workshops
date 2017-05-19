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
    private $password;

    /**
     * ChangePasswordCommand constructor.
     * @param UserId $userId
     * @param UserPassword $password
     */
    public function __construct(
        UserId $userId,
        UserPassword $password
    )
    {
        $this->userId = $userId;
        $this->password = $password;
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
    public function getPassword(): UserPassword
    {
        return $this->password;
    }
}