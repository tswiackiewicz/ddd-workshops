<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Event\UserEventHandler;
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    ActiveUser, RegisteredUser, UserLogin, UserRepository
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
     * @return UserRepository
     */
    protected function getUserRepositoryMockForRegisterUser(): UserRepository
    {
        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
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
     * @return UserRepository
     */
    protected function getUserRepositoryMockWhenUserAlreadyExists(): UserRepository
    {
        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
            ->setMethods([
                'exists'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('exists')->willReturn(true);

        return $repository;
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepositoryMockForActivateUser(): UserRepository
    {
        $user = $this->getRegisteredUser();

        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
            ->setMethods([
                'getRegisteredUserByHash',
                'save'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('getRegisteredUserByHash')->willReturn($user);
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
     * @return UserRepository
     */
    protected function getUserRepositoryMockForEnableUser(): UserRepository
    {
        $user = $this->getActiveUser();

        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
            ->setMethods([
                'getActiveUserById',
                'save'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('getActiveUserById')->willReturn($user);
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
     * @return UserRepository
     */
    protected function getUserRepositoryMockWhenRegisteredUserByHashNotFound(): UserRepository
    {
        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
            ->setMethods([
                'getRegisteredUserByHash'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())
            ->method('getRegisteredUserByHash')
            ->willThrowException(UserNotFoundException::forUser($this->login));

        return $repository;
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepositoryMockWhenActiveUserByIdNotFound(): UserRepository
    {
        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
            ->setMethods([
                'getActiveUserById'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())
            ->method('getActiveUserById')
            ->willThrowException(UserNotFoundException::forUser($this->login));

        return $repository;
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepositoryMockWhenUserByIdNotFound(): UserRepository
    {
        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
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
     * @return UserRepository
     */
    protected function getUserRepositoryMockForRemoveUser(): UserRepository
    {
        $user = $this->getActiveUser();

        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
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
