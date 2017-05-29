<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Event\UserEventHandler;
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    ActiveUser, ActiveUserRepository, Password\UserPasswordService, RegisteredUser, RegisteredUserRepository, User, UserLogin, UserRepository
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserNotifier;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

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
        $inactiveUser = $this->getUser(false, false);

        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
            ->setMethods([
                'getByHash',
                'save'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('getByHash')->willReturn($inactiveUser);
        $repository->expects(self::once())->method('save');

        return $repository;
    }

    /**
     * @param bool $active
     * @param bool $enabled
     * @return User
     */
    protected function getUser(bool $active, bool $enabled): User
    {
        return new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            $active,
            $enabled
        );
    }

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
        $repository->expects(self::once())->method('getById')->willReturn($user ?: $this->getUser(true, true));

        return $repository;
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepositoryMockWhenUserByHashNotFound(): UserRepository
    {
        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
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
}
