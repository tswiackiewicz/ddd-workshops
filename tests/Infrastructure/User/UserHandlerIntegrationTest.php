<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Infrastructure\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TSwiackiewicz\AwesomeApp\Application\User\Command\ActivateUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\ChangePasswordCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\DisableUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\EnableUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\RegisterUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Command\UnregisterUserCommand;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\ActivateUserHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\ChangePasswordHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\DisableUserHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\EnableUserHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\RegisterUserHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\UnregisterUserHandler;
use TSwiackiewicz\AwesomeApp\Domain\User\Exception\PasswordException;
use TSwiackiewicz\AwesomeApp\Domain\User\Exception\UserAlreadyExistsException;
use TSwiackiewicz\AwesomeApp\Domain\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserLogin;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserPassword;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;
use TSwiackiewicz\AwesomeApp\Infrastructure\Persistence\InMemoryUserReadModelRepository;
use TSwiackiewicz\AwesomeApp\SharedKernel\Exception\InvalidArgumentException;

#[CoversClass(RegisterUserHandler::class)]
#[CoversClass(ActivateUserHandler::class)]
#[CoversClass(EnableUserHandler::class)]
#[CoversClass(DisableUserHandler::class)]
#[CoversClass(ChangePasswordHandler::class)]
#[CoversClass(UnregisterUserHandler::class)]
class UserHandlerIntegrationTest extends UserHandlerIntegrationBaseTestCase
{
    #[Test]
    public function shouldRegisterUser(): void
    {
        $this->clearCache();

        $registeredUserId = ($this->registerHandler)(
            new RegisterUserCommand(
                new UserLogin($this->login),
                new UserPassword($this->password)
            )
        );

        self::assertEquals(UserId::fromInt($this->userId), $registeredUserId);

        $nextRegisteredUserId = ($this->registerHandler)(
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

        ($this->registerHandler)(
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

        ($this->registerHandler)(
            new RegisterUserCommand(
                new UserLogin('invalid_login'),
                new UserPassword($this->password)
            )
        );
    }

    #[Test]
    public function shouldActivateUser(): void
    {
        ($this->activateHandler)(
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

        ($this->activateHandler)(
            new ActivateUserCommand('non_existent_user_hash')
        );
    }

    #[Test]
    public function shouldEnableUser(): void
    {
        $this->disableUser();

        ($this->enableHandler)(
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

        ($this->enableHandler)(
            new EnableUserCommand(UserId::fromInt(1234))
        );
    }

    #[Test]
    public function shouldDisableUser(): void
    {
        $this->enableUser();

        ($this->disableHandler)(
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

        ($this->disableHandler)(
            new DisableUserCommand(UserId::fromInt(1234))
        );
    }

    #[Test]
    public function shouldChangePassword(): void
    {
        $this->enableUser();
        $newPassword = 'new-VEEERY_StR0Ng_P@sSw0rD1!#';

        ($this->changePasswordHandler)(
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

        ($this->changePasswordHandler)(
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

        ($this->changePasswordHandler)(
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

        ($this->changePasswordHandler)(
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

        ($this->unregisterHandler)(
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

        ($this->unregisterHandler)(
            new UnregisterUserCommand(
                UserId::fromInt(1234)
            )
        );
    }
}
