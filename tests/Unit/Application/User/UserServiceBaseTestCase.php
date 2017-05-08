<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Event\UserEventHandler;
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    ActiveUser, ActiveUserRepository, RegisteredUser, RegisteredUserRepository, UserLogin
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
                'getById',
                'remove'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('getById')->willReturn($user);
        $repository->expects(self::once())->method('remove');

        return $repository;
    }

    /**
     * @return UserNotifier|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getUserNotifierMock(): UserNotifier
    {
        return $this->getMockBuilder(UserNotifier::class)
            ->setMethods([
                'notifyUser'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
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
     * Setup fixtures
     */
    protected function setUp(): void
    {
        FakeEventBus::clearRegisteredHandlers();
    }
}
