<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\PasswordException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\DomainModel\User\User;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

#[CoversClass(User::class)]
class UserTest extends UserBaseTestCase
{
    #[Test]
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

    #[Test]
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

    #[Test]
    public function shouldActivateUser(): void
    {
        $user = $this->createInactiveUser();
        $user->activate();

        self::assertTrue($user->isActive());
        self::assertTrue($user->isEnabled());
    }

    #[Test]
    public function shouldFailWhenActivateAlreadyActivatedUser(): void
    {
        $this->expectException(UserException::class);

        $user = $this->createActiveUser();
        $user->activate();
    }

    #[Test]
    public function shouldEnableUser(): void
    {
        $user = $this->createDisabledUser();
        $user->enable();

        self::assertTrue($user->isEnabled());
    }

    #[Test]
    public function shouldFailWhenEnableInactiveUser(): void
    {
        $this->expectException(UserException::class);

        $user = $this->createInactiveUser();
        $user->enable();
    }

    #[Test]
    public function shouldFailWhenEnableAlreadyEnabledUser(): void
    {
        $this->expectException(UserException::class);

        $user = $this->createEnabledUser();
        $user->enable();
    }

    #[Test]
    public function shouldDisableUser(): void
    {
        $user = $this->createEnabledUser();
        $user->disable();

        self::assertFalse($user->isEnabled());
    }

    #[Test]
    public function shouldFailWhenDisableInactiveUser(): void
    {
        $this->expectException(UserException::class);

        $user = $this->createInactiveUser();
        $user->disable();
    }

    #[Test]
    public function shouldFailWhenDisableAlreadyDisabledUser(): void
    {
        $this->expectException(UserException::class);

        $user = $this->createDisabledUser();
        $user->disable();
    }

    #[Test]
    public function shouldChangePassword(): void
    {
        $user = $this->createEnabledUser();
        $user->changePassword(new UserPassword('newPassword1234'));

        self::assertTrue($user->getPassword()->equals(new UserPassword('newPassword1234')));
    }

    #[Test]
    public function shouldFailWhenPasswordChangedByInactiveUser(): void
    {
        $this->expectException(UserException::class);

        $user = $this->createInactiveUser();
        $user->changePassword(new UserPassword('newPassword1234'));
    }

    #[Test]
    public function shouldFailWhenPasswordChangedByDisabledUser(): void
    {
        $this->expectException(UserException::class);

        $user = $this->createDisabledUser();
        $user->changePassword(new UserPassword('newPassword1234'));
    }

    #[Test]
    public function shouldFailWhenChangedPasswordEqualsWithCurrentPassword(): void
    {
        $this->expectException(PasswordException::class);

        $user = $this->createEnabledUser();
        $user->changePassword(new UserPassword($this->password));
    }
}
