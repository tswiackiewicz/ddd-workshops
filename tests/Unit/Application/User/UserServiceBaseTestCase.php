<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Password\UserPasswordService, User, UserLogin, UserProjector, UserRepository
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserNotifier;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;
use TSwiackiewicz\DDD\EventSourcing\AggregateHistory;
use TSwiackiewicz\DDD\EventStore\EventStore;

/**
 * Class UserServiceBaseTestCase
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User
 */
abstract class UserServiceBaseTestCase extends UserBaseTestCase
{
    /**
     * @return UserRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getUserRepositoryMock(): UserRepository
    {
        return $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param null|User $user
     * @return UserRepository
     */
    protected function getUserRepositoryMockReturningUser(?User $user = null): UserRepository
    {
        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
            ->setMethods([
                'getById'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('getById')->willReturn($user ?: $this->getUserMock(true, true));

        return $repository;
    }

    /**
     * @param bool $active
     * @param bool $enabled
     * @return User
     */
    protected function getUserMock(bool $active, bool $enabled): User
    {
        /** @var UserId $userId */
        $userId = UserId::fromInt($this->userId);

        $user = User::reconstituteFrom(
            new AggregateHistory($userId, [])
        );
        $refUser = new \ReflectionObject($user);
        $refProperty = $refUser->getProperty('login');
        $refProperty->setAccessible(true);
        $refProperty->setValue($user, new UserLogin($this->login));

        $refProperty = $refUser->getProperty('password');
        $refProperty->setAccessible(true);
        $refProperty->setValue($user, new UserPassword($this->password));

        $refProperty = $refUser->getProperty('active');
        $refProperty->setAccessible(true);
        $refProperty->setValue($user, $active);

        $refProperty = $refUser->getProperty('enabled');
        $refProperty->setAccessible(true);
        $refProperty->setValue($user, $enabled);

        return $user;
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
     * @return EventStore|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEventStoreMock(): EventStore
    {
        return $this->getMockBuilder(EventStore::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return UserProjector|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getUserProjectorMock(): UserProjector
    {
        return $this->getMockBuilder(UserProjector::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Setup fixtures
     */
    protected function setUp(): void
    {
        FakeEventBus::clearRegisteredHandlers();
    }
}
