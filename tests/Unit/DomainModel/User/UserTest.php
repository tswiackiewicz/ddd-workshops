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

        self::assertEquals(UserId::fromInt($this->userId), $registeredUser->getId());
        self::assertEquals($this->login, (string)$registeredUser->getLogin());
        self::assertEquals($this->password, (string)$registeredUser->getPassword());
        self::assertFalse($registeredUser->isActive());
        self::assertFalse($registeredUser->isEnabled());
        self::assertEquals($this->hash, $registeredUser->hash());
    }

    /**
     * @test
     */
    public function shouldCreateFromNative(): void
    {
        $user = User::fromNative(
            [
                'id' => $this->userId,
                'login' => $this->login,
                'password' => $this->password
            ]
        );

        self::assertInstanceOf(User::class, $user);
    }

    /**
     * @test
     */
    public function shouldActivateUser(): void
    {
        $user = $this->createInactiveUser();
        $user->activate();

        self::assertTrue($user->isActive());
        self::assertTrue($user->isEnabled());
    }

    /**
     * @test
     */
    public function shouldFailWhenActivateAlreadyActivatedUser(): void
    {
        $this->expectException(UserException::class);

        $user = $this->createActiveUser();
        $user->activate();
    }

    /**
     * @test
     */
    public function shouldEnableUser(): void
    {
        $user = $this->createDisabledUser();
        $user->enable();

        self::assertTrue($user->isEnabled());
    }

    /**
     * @test
     */
    public function shouldFailWhenEnableInactiveUser(): void
    {
        $this->expectException(UserException::class);

        $user = $this->createInactiveUser();
        $user->enable();
    }

    /**
     * @test
     */
    public function shouldFailWhenEnableAlreadyEnabledUser(): void
    {
        $this->expectException(UserException::class);

        $user = $this->createEnabledUser();
        $user->enable();
    }

    /**
     * @test
     */
    public function shouldDisableUser(): void
    {
        $user = $this->createEnabledUser();
        $user->disable();

        self::assertFalse($user->isEnabled());
    }

    /**
     * @test
     */
    public function shouldFailWhenDisableInactiveUser(): void
    {
        $this->expectException(UserException::class);

        $user = $this->createInactiveUser();
        $user->disable();
    }

    /**
     * @test
     */
    public function shouldFailWhenDisableAlreadyDisabledUser(): void
    {
        $this->expectException(UserException::class);

        $user = $this->createDisabledUser();
        $user->disable();
    }

    /**
     * @test
     */
    public function shouldChangePassword(): void
    {
        $user = $this->createEnabledUser();
        $user->changePassword(new UserPassword('newPassword1234'));

        self::assertTrue($user->getPassword()->equals(new UserPassword('newPassword1234')));
    }

    /**
     * @test
     */
    public function shouldFailWhenPasswordChangedByInactiveUser(): void
    {
        $this->expectException(UserException::class);

        $user = $this->createInactiveUser();
        $user->changePassword(new UserPassword('newPassword1234'));
    }

    /**
     * @test
     */
    public function shouldFailWhenPasswordChangedByDisabledUser(): void
    {
        $this->expectException(UserException::class);

        $user = $this->createDisabledUser();
        $user->changePassword(new UserPassword('newPassword1234'));
    }

    /**
     * @test
     */
    public function shouldFailWhenChangedPasswordEqualsWithCurrentPassword(): void
    {
        $this->expectException(PasswordException::class);

        $user = $this->createEnabledUser();
        $user->changePassword(new UserPassword($this->password));
    }
}
