<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Integration\Application\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    ActivateUserCommand, ChangePasswordCommand, DisableUserCommand, EnableUserCommand, RegisterUserCommand, UnregisterUserCommand
};
use TSwiackiewicz\AwesomeApp\Application\User\UserService;
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Exception\PasswordException, Exception\UserAlreadyExistsException, Exception\UserNotFoundException, Password\UserPassword, UserLogin
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    User\InMemoryUserReadModelRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

#[CoversClass(UserService::class)]
class UserServiceTest extends UserServiceBaseTestCase
{
    #[Test]
    public function shouldRegisterUser(): void
    {
        $this->clearCache();

        $registeredUserId = $this->service->register(
            new RegisterUserCommand(
                new UserLogin($this->login),
                new UserPassword($this->password)
            )
        );

        self::assertEquals(UserId::fromInt($this->userId), $registeredUserId);

        $nextRegisteredUserId = $this->service->register(
            new RegisterUserCommand(
                new UserLogin('next.' . $this->login),
                new UserPassword($this->password)
            )
        );

        self::assertEquals(UserId::fromInt($this->userId + 1), $nextRegisteredUserId);
    }

    #[Test]
    public function shouldFailWhenRegisteredUserAlreadyExists(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $this->service->register(
            new RegisterUserCommand(
                new UserLogin($this->login),
                new UserPassword($this->password)
            )
        );
    }

    #[Test]
    public function shouldFailWhenRegisteredUserLoginIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->register(
            new RegisterUserCommand(
                new UserLogin('invalid_login'),
                new UserPassword($this->password)
            )
        );
    }

    #[Test]
    public function shouldActivateUser(): void
    {
        $this->service->activate(
            new ActivateUserCommand($this->hash)
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById(UserId::fromInt($this->userId));

        self::assertTrue($userDTO->isActive());
    }

    #[Test]
    public function shouldFailWhenActivatedUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->service->activate(
            new ActivateUserCommand('non_existent_user_hash')
        );
    }

    #[Test]
    public function shouldEnableUser(): void
    {
        $this->disableUser();

        $this->service->enable(
            new EnableUserCommand(UserId::fromInt($this->userId))
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById(UserId::fromInt($this->userId));

        self::assertTrue($userDTO->isEnabled());
    }

    #[Test]
    public function shouldFailWhenEnabledUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->service->enable(
            new EnableUserCommand(UserId::fromInt(1234))
        );
    }

    #[Test]
    public function shouldDisableUser(): void
    {
        $this->enableUser();

        $this->service->disable(
            new DisableUserCommand(UserId::fromInt($this->userId))
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById(UserId::fromInt($this->userId));

        self::assertFalse($userDTO->isEnabled());
    }

    #[Test]
    public function shouldFailWhenDisabledUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->service->disable(
            new DisableUserCommand(UserId::fromInt(1234))
        );
    }

    #[Test]
    public function shouldChangePassword(): void
    {
        $this->enableUser();
        $newPassword = 'new-VEEERY_StR0Ng_P@sSw0rD1!#';

        $this->service->changePassword(
            new ChangePasswordCommand(
                UserId::fromInt($this->userId),
                new UserPassword($newPassword)
            )
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById(UserId::fromInt($this->userId));

        self::assertEquals($newPassword, $userDTO->getPassword());
    }

    #[Test]
    public function shouldFailWhenChangedPasswordIsTooWeak(): void
    {
        $this->enableUser();

        $this->expectException(PasswordException::class);

        $this->service->changePassword(
            new ChangePasswordCommand(
                UserId::fromInt($this->userId),
                new UserPassword('weak_password')
            )
        );
    }

    #[Test]
    public function shouldFailWhenChangedPasswordEqualsWithCurrentPassword(): void
    {
        $this->enableUser();

        $this->expectException(PasswordException::class);

        $this->service->changePassword(
            new ChangePasswordCommand(
                UserId::fromInt($this->userId),
                new UserPassword($this->password)
            )
        );
    }

    #[Test]
    public function shouldFailWhenUserThatChangedPasswordNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->service->changePassword(
            new ChangePasswordCommand(
                UserId::fromInt(1234),
                new UserPassword($this->password)
            )
        );
    }

    #[Test]
    public function shouldRemoveUser(): void
    {
        $this->enableUser();

        $this->service->unregister(
            new UnregisterUserCommand(
                UserId::fromInt($this->userId)
            )
        );

        $repository = new InMemoryUserReadModelRepository();
        $userDTO = $repository->findById(UserId::fromInt(1));

        self::assertNull($userDTO);
    }

    #[Test]
    public function shouldFailWhenRemovedUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->service->unregister(
            new UnregisterUserCommand(
                UserId::fromInt(1234)
            )
        );
    }
}
