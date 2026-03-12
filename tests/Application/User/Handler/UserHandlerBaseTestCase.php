<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Application\User\Handler;

use TSwiackiewicz\AwesomeApp\Domain\User\Entity\User;
use TSwiackiewicz\AwesomeApp\Domain\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\Domain\User\Repository\UserRepository;
use TSwiackiewicz\AwesomeApp\Domain\User\Service\UserPasswordService;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserLogin;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserPassword;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserStatus;
use TSwiackiewicz\AwesomeApp\Tests\UserBaseTestCase;

abstract class UserHandlerBaseTestCase extends UserBaseTestCase
{
    protected function getUserRepositoryMockForRegisterUser(): UserRepository
    {
        $repository = $this->createMock(UserRepository::class);
        $repository->expects(self::once())->method('exists')->willReturn(false);
        $repository->expects(self::once())->method('nextIdentity')->willReturn(UserId::nullInstance());
        $repository->expects(self::once())->method('save')->willReturn(UserId::fromInt($this->userId));

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
        $inactiveUser = $this->getUser(UserStatus::INACTIVE);

        $repository = $this->createMock(UserRepository::class);
        $repository->expects(self::once())->method('getByHash')->willReturn($inactiveUser);
        $repository->expects(self::once())->method('save')->willReturn(UserId::fromInt($this->userId));

        return $repository;
    }

    protected function getUser(UserStatus $status): User
    {
        return new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            $status
        );
    }

    protected function getUserRepositoryMock(): UserRepository
    {
        return $this->createMock(UserRepository::class);
    }

    protected function getUserRepositoryMockReturningUser(?User $user = null): UserRepository
    {
        $repository = $this->createMock(UserRepository::class);
        $repository->expects(self::once())->method('getById')->willReturn($user ?: $this->getUser(UserStatus::ACTIVE));
        $repository->method('save')->willReturn(UserId::fromInt($this->userId));

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
