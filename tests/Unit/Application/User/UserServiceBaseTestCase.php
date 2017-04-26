<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    LoggedInUser, RegisteredUser, UserLogin, UserRepository
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
        $user = $this->getRegisteredUser();

        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
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
     * @return UserRepository
     */
    protected function getUserRepositoryMockForEnableUser(): UserRepository
    {
        $user = $this->getLoggedInUser();

        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
            ->setMethods([
                'getByLogin',
                'save'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('getByLogin')->willReturn($user);
        $repository->expects(self::once())->method('save');

        return $repository;
    }

    /**
     * @return LoggedInUser
     */
    protected function getLoggedInUser(): LoggedInUser
    {
        return new LoggedInUser(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            false
        );
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
    protected function getUserRepositoryMockWhenUserByLoginNotFound(): UserRepository
    {
        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
            ->setMethods([
                'getByLogin'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())
            ->method('getByLogin')
            ->willThrowException(UserNotFoundException::forUser($this->login));

        return $repository;
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepositoryMockForRemoveUser(): UserRepository
    {
        $user = $this->getLoggedInUser();

        /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(UserRepository::class)
            ->setMethods([
                'getByLogin',
                'remove'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repository->expects(self::once())->method('getByLogin')->willReturn($user);
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
}
