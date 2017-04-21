<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\LoggedInUser;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserId;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserPassword;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class LoggedInUserTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User
 */
class LoggedInUserTest extends UserBaseTestCase
{
    /**
     * @test
     */
    public function shouldEnableUser(): void
    {
        $user = new LoggedInUser(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            false
        );
        $user->enable();

        self::assertTrue($user->isEnabled());
    }

    /**
     * @test
     */
    public function shouldDisableUser(): void
    {
        $user = new LoggedInUser(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            true
        );
        $user->disable();

        self::assertFalse($user->isEnabled());
    }

    /**
     * @test
     */
    public function shouldChangePassword(): void
    {
        $user = new LoggedInUser(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            true
        );
        $user->changePassword(new UserPassword('newPassword1234'));

        self::assertEquals('newPassword1234', $user->getPassword());
    }
}
