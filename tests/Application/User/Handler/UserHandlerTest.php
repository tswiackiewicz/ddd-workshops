<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Application\User\Handler;

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
use TSwiackiewicz\AwesomeApp\Application\User\Handler\UserActivatedEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\UserDisabledEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\UserEnabledEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\UserPasswordChangedEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\UserRegisteredEventHandler;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\UserUnregisteredEventHandler;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserActivatedEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserDisabledEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserEnabledEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserPasswordChangedEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserRegisteredEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserUnregisteredEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Exception\PasswordException;
use TSwiackiewicz\AwesomeApp\Domain\User\Exception\UserAlreadyExistsException;
use TSwiackiewicz\AwesomeApp\Domain\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserLogin;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserPassword;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserStatus;
use TSwiackiewicz\DDD\Event\EventBus;

#[CoversClass(RegisterUserHandler::class)]
#[CoversClass(ActivateUserHandler::class)]
#[CoversClass(EnableUserHandler::class)]
#[CoversClass(DisableUserHandler::class)]
#[CoversClass(ChangePasswordHandler::class)]
#[CoversClass(UnregisterUserHandler::class)]
class UserHandlerTest extends UserHandlerBaseTestCase
{
    #[Test]
    public function shouldRegisterUser(): void
    {
        EventBus::subscribe(
            UserRegisteredEvent::class,
            new UserRegisteredEventHandler(
                $this->getUserNotifierMock(UserRegisteredEvent::class)
            )
        );

        $handler = new RegisterUserHandler(
            $this->getUserRepositoryMockForRegisterUser(),
            $this->getUserPasswordServiceMock()
        );
        $handler(
            new RegisterUserCommand(new UserLogin($this->login), new UserPassword($this->password))
        );
    }

    #[Test]
    public function shouldFailWhenRegisteredUserAlreadyExists(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $handler = new RegisterUserHandler(
            $this->getUserRepositoryMockWhenUserAlreadyExists(),
            $this->getUserPasswordServiceMock()
        );
        $handler(
            new RegisterUserCommand(new UserLogin($this->login), new UserPassword($this->password))
        );
    }

    #[Test]
    public function shouldActivateUser(): void
    {
        EventBus::subscribe(
            UserActivatedEvent::class,
            new UserActivatedEventHandler(
                $this->getUserNotifierMock(UserActivatedEvent::class)
            )
        );

        $handler = new ActivateUserHandler(
            $this->getUserRepositoryMockForActivateUser()
        );
        $handler(
            new ActivateUserCommand('existent_user_hash')
        );
    }

    #[Test]
    public function shouldFailWhenActivatedUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $handler = new ActivateUserHandler(
            $this->getUserRepositoryMockWhenUserByHashNotFound()
        );
        $handler(
            new ActivateUserCommand('non_existent_user_hash')
        );
    }

    #[Test]
    public function shouldEnableUser(): void
    {
        $disabledUser = $this->getUser(UserStatus::DISABLED);

        EventBus::subscribe(
            UserEnabledEvent::class,
            new UserEnabledEventHandler(
                $this->getUserNotifierMock(UserEnabledEvent::class)
            )
        );

        $handler = new EnableUserHandler(
            $this->getUserRepositoryMockReturningUser($disabledUser)
        );
        $handler(
            new EnableUserCommand(UserId::fromInt($this->userId))
        );
    }

    #[Test]
    public function shouldFailWhenEnabledUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $handler = new EnableUserHandler(
            $this->getUserRepositoryMockWhenUserByIdNotFound()
        );
        $handler(
            new EnableUserCommand(UserId::fromInt(1234))
        );
    }

    #[Test]
    public function shouldDisableUser(): void
    {
        $enabledUser = $this->getUser(UserStatus::ACTIVE);

        EventBus::subscribe(
            UserDisabledEvent::class,
            new UserDisabledEventHandler(
                $this->getUserNotifierMock(UserDisabledEvent::class)
            )
        );

        $handler = new DisableUserHandler(
            $this->getUserRepositoryMockReturningUser($enabledUser)
        );
        $handler(
            new DisableUserCommand(UserId::fromInt($this->userId))
        );
    }

    #[Test]
    public function shouldFailWhenDisabledUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $handler = new DisableUserHandler(
            $this->getUserRepositoryMockWhenUserByIdNotFound()
        );
        $handler(
            new DisableUserCommand(UserId::fromInt(1234))
        );
    }

    #[Test]
    public function shouldChangePassword(): void
    {
        $newPassword = 'new-VEEERY_StR0Ng_P@sSw0rD1!#';
        $enabledUser = $this->getUser(UserStatus::ACTIVE);

        EventBus::subscribe(
            UserPasswordChangedEvent::class,
            new UserPasswordChangedEventHandler(
                $this->getUserNotifierMock(UserPasswordChangedEvent::class)
            )
        );

        $handler = new ChangePasswordHandler(
            $this->getUserRepositoryMockReturningUser($enabledUser),
            $this->getUserPasswordServiceMock()
        );
        $handler(
            new ChangePasswordCommand(
                UserId::fromInt($this->userId),
                new UserPassword($newPassword)
            )
        );
    }

    #[Test]
    public function shouldFailWhenChangedPasswordIsTooWeak(): void
    {
        $this->expectException(PasswordException::class);

        $handler = new ChangePasswordHandler(
            $this->getUserRepositoryMock(),
            $this->getUserPasswordServiceMockForWeakPasswordVerification()
        );
        $handler(
            new ChangePasswordCommand(
                UserId::fromInt($this->userId),
                new UserPassword('weak_password')
            )
        );
    }

    #[Test]
    public function shouldFailWhenUserChangingPasswordNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $handler = new ChangePasswordHandler(
            $this->getUserRepositoryMockWhenUserByIdNotFound(),
            $this->getUserPasswordServiceMock()
        );
        $handler(
            new ChangePasswordCommand(
                UserId::fromInt(1234),
                new UserPassword($this->password)
            )
        );
    }

    #[Test]
    public function shouldRemoveUser(): void
    {
        $enabledUser = $this->getUser(UserStatus::ACTIVE);

        EventBus::subscribe(
            UserUnregisteredEvent::class,
            new UserUnregisteredEventHandler(
                $this->getUserNotifierMock(UserUnregisteredEvent::class)
            )
        );

        $handler = new UnregisterUserHandler(
            $this->getUserRepositoryMockReturningUser($enabledUser)
        );
        $handler(
            new UnregisterUserCommand(UserId::fromInt($this->userId))
        );
    }

    #[Test]
    public function shouldFailWhenRemovedUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $handler = new UnregisterUserHandler(
            $this->getUserRepositoryMockWhenUserByIdNotFound()
        );
        $handler(
            new UnregisterUserCommand(UserId::fromInt($this->userId))
        );
    }
}
