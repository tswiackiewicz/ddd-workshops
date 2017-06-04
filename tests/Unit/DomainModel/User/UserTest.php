<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\PasswordException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserException;
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
        self::assertEquals($this->hash, $registeredUser->hash());
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

    /**
     * @test
     */
    public function shouldFailWhenActivateAlreadyActivatedUser(): void
    {
        $this->expectException(UserException::class);

        $user = new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            true,
            false
        );
        $user->activate();
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

    /**
     * @test
     */
    public function shouldFailWhenEnableInactiveUser(): void
    {
        $this->expectException(UserException::class);

        $user = new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            false,
            false
        );
        $user->enable();
    }

    /**
     * @test
     */
    public function shouldFailWhenEnableAlreadyEnabledUser(): void
    {
        $this->expectException(UserException::class);

        $user = new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            true,
            true
        );
        $user->enable();
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

    /**
     * @test
     */
    public function shouldFailWhenDisableInactiveUser(): void
    {
        $this->expectException(UserException::class);

        $user = new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            false,
            true
        );
        $user->disable();
    }

    /**
     * @test
     */
    public function shouldFailWhenDisableAlreadyDisabledUser(): void
    {
        $this->expectException(UserException::class);

        $user = new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            true,
            false
        );
        $user->disable();
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

    /**
     * @test
     */
    public function shouldFailWhenPasswordChangedByInactiveUser(): void
    {
        $this->expectException(UserException::class);

        $user = new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            false,
            true
        );
        $user->changePassword(new UserPassword('newPassword1234'));
    }

    /**
     * @test
     */
    public function shouldFailWhenPasswordChangedByDisabledUser(): void
    {
        $this->expectException(UserException::class);

        $user = new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            true,
            false
        );
        $user->changePassword(new UserPassword('newPassword1234'));
    }

    /**
     * @test
     */
    public function shouldFailWhenChangedPasswordEqualsWithCurrentPassword(): void
    {
        $this->expectException(PasswordException::class);

        $user = new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            true,
            true
        );
        $user->changePassword(new UserPassword($this->password));
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
