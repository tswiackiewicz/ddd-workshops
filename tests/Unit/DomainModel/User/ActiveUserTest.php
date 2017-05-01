<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\ActiveUser;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class ActiveUserTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User
 *
 * @coversDefaultClass ActiveUser
 */
class ActiveUserTest extends UserBaseTestCase
{
    /**
     * @test
     */
    public function shouldEnableUser(): void
    {
        $user = new ActiveUser(
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
        $user = new ActiveUser(
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
        $user = new ActiveUser(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            true
        );
        $user->changePassword(new UserPassword('newPassword1234'));

        self::assertEquals('newPassword1234', $user->getPassword());
    }
}
