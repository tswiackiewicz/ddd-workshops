<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Password\UserPasswordService, User, UserLogin, UserRepository
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPassword;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

abstract class UserServiceBaseTestCase extends UserBaseTestCase
{
    protected function getUserRepositoryMockForRegisterUser(): UserRepository
    {
        $repository = $this->createMock(UserRepository::class);
        $repository->expects(self::once())->method('exists')->willReturn(false);
        $repository->expects(self::once())->method('nextIdentity')->willReturn(UserId::nullInstance());
        $repository->expects(self::once())->method('save');

        return $repository;
    }

    protected function getUserRepositoryMockWhenUserAlreadyExists(): UserRepository
    {
        $repository = $this->createMock(UserRepository::class);
        $repository->expects(self::once())->method('exists')->willReturn(true);

        return $repository;
    }

    protected function getUserRepositoryMockForActivateUser(): UserRepository
    {
        $inactiveUser = $this->getUser(false, false);

        $repository = $this->createMock(UserRepository::class);
        $repository->expects(self::once())->method('getByHash')->willReturn($inactiveUser);
        $repository->expects(self::once())->method('save');

        return $repository;
    }

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

    protected function getUserRepositoryMock(): UserRepository
    {
        return $this->createMock(UserRepository::class);
    }

    protected function getUserRepositoryMockReturningUser(?User $user = null): UserRepository
    {
        $repository = $this->createMock(UserRepository::class);
        $repository->expects(self::once())->method('getById')->willReturn($user ?: $this->getUser(true, true));

        return $repository;
    }

    protected function getUserRepositoryMockWhenUserByHashNotFound(): UserRepository
    {
        $repository = $this->createMock(UserRepository::class);
        $repository->expects(self::once())
            ->method('getByHash')
            ->willThrowException(UserNotFoundException::forUser($this->login));

        return $repository;
    }

    protected function getUserRepositoryMockWhenUserByIdNotFound(): UserRepository
    {
        $repository = $this->createMock(UserRepository::class);
        $repository->expects(self::once())
            ->method('getById')
            ->willThrowException(UserNotFoundException::forUser($this->login));

        return $repository;
    }

    protected function getUserPasswordServiceMock(): UserPasswordService
    {
        return $this->createMock(UserPasswordService::class);
    }

    protected function getUserPasswordServiceMockForWeakPasswordVerification(): UserPasswordService
    {
        $service = $this->createMock(UserPasswordService::class);
        $service->expects(self::once())->method('isWeak')->willReturn(true);

        return $service;
    }
}
