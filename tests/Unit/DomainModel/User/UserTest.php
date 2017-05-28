<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\ActiveUser;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\DomainModel\User\User;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class UserTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User
 *
 * @coversDefaultClass User
 */
class UserTest extends UserBaseTestCase
{
    /**
     * @test
     */
    public function shouldRegisterUser(): void
    {
        $registeredUser = User::register(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password)
        );

        self::assertEquals($this->userId, $registeredUser->getId()->getId());
        self::assertEquals('94b3e2c871ff1b3e4e03c74cd9c501f5', $registeredUser->hash());
    }

    /**
     * @test
     */
    public function shouldActivateUser(): void
    {
        $user = new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            false,
            false
        );
        $user->activate();

        self::assertTrue($user->isActive());
    }

    public function shouldFailWhenActivateAlreadyActivatedUser(): void
    {

    }

    /**
     * @test
     */
    public function shouldEnableUser(): void
    {
        $user = new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            true,
            false
        );
        $user->enable();

        self::assertTrue($user->isEnabled());
    }

    public function shouldFailWhenEnableInactiveUser(): void
    {

    }

    public function shouldFailWhenEnableAlreadyEnabledUser(): void
    {

    }

    /**
     * @test
     */
    public function shouldDisableUser(): void
    {
        $user = new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            true,
            true
        );
        $user->disable();

        self::assertFalse($user->isEnabled());
    }

    public function shouldFailWhenDisableInactiveUser(): void
    {

    }

    public function shouldFailWhenDisableAlreadyDisabledUser(): void
    {

    }

    /**
     * @test
     */
    public function shouldChangePassword(): void
    {
        $user = new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            true,
            true
        );
        $user->changePassword(new UserPassword('newPassword1234'));

        self::assertTrue($user->getPassword()->equals(new UserPassword('newPassword1234')));
    }

    public function shouldFailWhenPasswordChangedByInactiveUser(): void
    {

    }

    public function shouldFailWhenPasswordChangedByDisabledUser(): void
    {

    }

    public function shouldFailWhenChangedPasswordEqualsWithCurrentPassword(): void
    {

    }

    /**
     * @test
     */
    public function shouldUnregisterUser(): void
    {
        $user = new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            true,
            true
        );
        $user->unregister();

        self::assertFalse($user->isActive());
        self::assertFalse($user->isEnabled());
    }
}
