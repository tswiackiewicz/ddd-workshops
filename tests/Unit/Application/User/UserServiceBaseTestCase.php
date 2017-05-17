<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\CommandValidator;
use TSwiackiewicz\AwesomeApp\Application\User\Event\UserEventHandler;
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    ActiveUser, ActiveUserRepository, Password\UserPasswordService, RegisteredUser, RegisteredUserRepository, UserLogin
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserNotifier;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;
use TSwiackiewicz\DDD\Event\EventHandler;

/**
 * Class UserServiceBaseTestCase
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User
 */
abstract class UserServiceBaseTestCase extends UserBaseTestCase
{
    /**
     * @return RegisteredUserRepository
     */
    protected function getRegisteredUserRepositoryMockForRegisterUser(): RegisteredUserRepository
    {
        /** @var RegisteredUserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(RegisteredUserRepository::class)
            ->setMethods([
                'nextIdentity',
                'exists',
                'save'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('exists')->willReturn(false);
        $repository->expects(self::once())->method('nextIdentity')->willReturn(UserId::nullInstance());
        $repository->expects(self::once())->method('save');

        return $repository;
    }

    /**
     * @return RegisteredUserRepository
     */
    protected function getRegisteredUserRepositoryMockWhenUserAlreadyExists(): RegisteredUserRepository
    {
        /** @var RegisteredUserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(RegisteredUserRepository::class)
            ->setMethods([
                'exists'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('exists')->willReturn(true);

        return $repository;
    }

    /**
     * @return RegisteredUserRepository
     */
    protected function getRegisteredUserRepositoryMockForActivateUser(): RegisteredUserRepository
    {
        $user = $this->getRegisteredUser();

        /** @var RegisteredUserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(RegisteredUserRepository::class)
            ->setMethods([
                'getByHash',
                'save'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('getByHash')->willReturn($user);
        $repository->expects(self::once())->method('save');

        return $repository;
    }

    /**
     * @return RegisteredUser
     */
    protected function getRegisteredUser(): RegisteredUser
    {
        return RegisteredUser::register(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password)
        );
    }

    /**
     * @return ActiveUserRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getActiveUserRepositoryMock(): ActiveUserRepository
    {
        return $this->getMockBuilder(ActiveUserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return ActiveUserRepository
     */
    protected function getActiveUserRepositoryMockForEnableUser(): ActiveUserRepository
    {
        $user = $this->getActiveUser();

        /** @var ActiveUserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(ActiveUserRepository::class)
            ->setMethods([
                'getById',
                'save'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('getById')->willReturn($user);
        $repository->expects(self::once())->method('save');

        return $repository;
    }

    /**
     * @return ActiveUser
     */
    protected function getActiveUser(): ActiveUser
    {
        return new ActiveUser(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            false
        );
    }

    /**
     * @return ActiveUserRepository
     */
    protected function getActiveUserRepositoryMockReturningActiveUser(): ActiveUserRepository
    {
        $user = $this->getActiveUser();

        /** @var ActiveUserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(ActiveUserRepository::class)
            ->setMethods([
                'getById'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('getById')->willReturn($user);

        return $repository;
    }

    /**
     * @return RegisteredUserRepository
     */
    protected function getRegisteredUserRepositoryMockWhenUserByHashNotFound(): RegisteredUserRepository
    {
        /** @var RegisteredUserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(RegisteredUserRepository::class)
            ->setMethods([
                'getByHash'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())
            ->method('getByHash')
            ->willThrowException(UserNotFoundException::forUser($this->login));

        return $repository;
    }

    /**
     * @return ActiveUserRepository
     */
    protected function getActiveUserRepositoryMockWhenUserByIdNotFound(): ActiveUserRepository
    {
        /** @var ActiveUserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(ActiveUserRepository::class)
            ->setMethods([
                'getById'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())
            ->method('getById')
            ->willThrowException(UserNotFoundException::forUser($this->login));

        return $repository;
    }

    /**
     * @return ActiveUserRepository
     */
    protected function getActiveUserRepositoryMockForRemoveUser(): ActiveUserRepository
    {
        $user = $this->getActiveUser();

        /** @var ActiveUserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(ActiveUserRepository::class)
            ->setMethods([
                'remove'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())
            ->method('remove')
            ->with($user->getId());

        return $repository;
    }

    /**
     * @param null|string $eventName
     * @return UserNotifier|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getUserNotifierMock(?string $eventName = null): UserNotifier
    {
        /** @var UserNotifier|\PHPUnit_Framework_MockObject_MockObject $notifier */
        $notifier = $this->getMockBuilder(UserNotifier::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'notifyUser'
            ])
            ->getMock();
        if ($eventName !== null) {
            $notifier->expects(self::once())
                ->method('notifyUser')
                ->with(self::isInstanceOf($eventName));
        }

        return $notifier;
    }

    /**
     * @param string $eventName
     * @return EventHandler
     */
    protected function getEventHandlerMock(string $eventName): EventHandler
    {
        /** @var UserNotifier|\PHPUnit_Framework_MockObject_MockObject $notifier */
        $notifier = $this->getMockBuilder(UserNotifier::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'notifyUser'
            ])
            ->getMock();
        $notifier->expects(self::once())
            ->method('notifyUser')
            ->with(self::isInstanceOf($eventName));

        return new UserEventHandler($notifier);
    }

    /**
     * @return CommandValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getCommandValidatorMock(): CommandValidator
    {
        return $this->getMockBuilder(CommandValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return UserPasswordService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getUserPasswordServiceMock(): UserPasswordService
    {
        return $this->getMockBuilder(UserPasswordService::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return UserPasswordService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getUserPasswordServiceMockForWeakPasswordVerification(): UserPasswordService
    {
        $service = $this->getMockBuilder(UserPasswordService::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'isWeak'
            ])
            ->getMock();
        $service->expects(self::once())->method('isWeak')->willReturn(true);

        return $service;
    }

    /**
     * Setup fixtures
     */
    protected function setUp(): void
    {
        FakeEventBus::clearRegisteredHandlers();
    }
}
