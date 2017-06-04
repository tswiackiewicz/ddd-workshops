<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\CommandValidator;
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
    protected function getUserRepositoryMockForRegisterUser(?User $user = null): UserRepository
    {
        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
            ->setMethods([
                'save',
                'getByLogin'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('save');
        $repository->expects(self::once())->method('getByLogin')->willReturn($user ?: $this->getUser(true, false));

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
     * @param null|User $user
     * @return UserRepository
     */
    protected function getUserRepositoryMockForActivateUser(?User $user = null): UserRepository
    {
        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
            ->setMethods([
                'getById',
                'save'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('getById')->willReturn($user ?: $this->getUser(true, false));
        $repository->expects(self::once())->method('save');

        return $repository;
    }

    /**
     * @param null|User $user
     * @return UserRepository
     */
    protected function getUserRepositoryMockForEnableUser(?User $user = null): UserRepository
    {
        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
            ->setMethods([
                'getById',
                'save'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('getById')->willReturn($user ?: $this->getUser(true, false));
        $repository->expects(self::once())->method('save');

        return $repository;
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
        $repository->expects(self::once())->method('getById')->willReturn($user ?: $this->getUser(true, false));

        return $repository;
    }

    /**
     * @param null|User $user
     * @return UserRepository
     */
    protected function getUserRepositoryMockReturningUserByHash(?User $user = null): UserRepository
    {
        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
            ->setMethods([
                'getByHash'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('getByHash')->willReturn($user ?: $this->getUser(true, false));

        return $repository;
    }

    /**
     * @param bool $exists
     * @return UserRepository
     */
    protected function getUserRepositoryMockCheckingIfUserByLoginExists(bool $exists): UserRepository
    {
        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
            ->setMethods([
                'exists'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('exists')->willReturn($exists);

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
            ->willThrowException(UserNotFoundException::forHash($this->hash));

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
        $user = $this->getUser(true, true);

        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
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
}
