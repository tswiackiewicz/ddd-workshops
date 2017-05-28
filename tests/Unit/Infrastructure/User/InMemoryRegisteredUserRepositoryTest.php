<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Exception\UserNotFoundException, Password\UserPassword, RegisteredUser, User, UserLogin
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryStorage, User\InMemoryRegisteredUserRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\UserRepositoryException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class InMemoryRegisteredUserRepositoryTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User
 *
 * @@coversDefaultClass InMemoryRegisteredUserRepository
 * @runTestsInSeparateProcesses
 */
class InMemoryRegisteredUserRepositoryTest extends UserBaseTestCase
{
    /**
     * @var InMemoryRegisteredUserRepository
     */
    private $repository;

    /**
     * @test
     */
    public function shouldReturnNextIdentity(): void
    {
        $userId = $this->repository->nextIdentity();

        self::assertInstanceOf(UserId::class, $userId);
    }

    /**
     * @test
     */
    public function shouldReturnTrueWhenUserExists(): void
    {
        self::assertTrue($this->repository->exists('registered_user@domain.com'));
    }

    /**
     * @test
     */
    public function shouldReturnFalseWhenUserNotExists(): void
    {
        self::assertFalse($this->repository->exists('non_existent_user'));
    }

    /**
     * @test
     */
    public function shouldReturnUserById(): void
    {
        $user = $this->repository->getById(
            UserId::fromInt(1)
        );

        self::assertInstanceOf(RegisteredUser::class, $user);
    }

    /**
     * @test
     */
    public function shouldFetchUserByIdFromStorageOnlyOnce(): void
    {
        $firstAttemptUser = $this->repository->getById(
            UserId::fromInt(1)
        );

        $repository = new InMemoryRegisteredUserRepository();
        $secondAttemptUser = $repository->getById(
            UserId::fromInt(1)
        );

        self::assertSame($firstAttemptUser, $secondAttemptUser);
    }

    /**
     * @test
     */
    public function shouldFailWhenDataInStorageIsInvalid(): void
    {
        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'id' => 1234,
                'login' => '',
                'password' => '',
                'hash' => '',
                'active' => true,
                'enabled' => true
            ]
        );
        $this->expectException(UserRepositoryException::class);

        $this->repository->getById(
            UserId::fromInt(1234)
        );
    }

    /**
     * @test
     */
    public function shouldFailWhenUserByIdNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->repository->getById(
            UserId::fromInt(1234)
        );
    }

    /**
     * @test
     */
    public function shouldReturnUserByHash(): void
    {
        $registeredUser = RegisteredUser::register(
            UserId::fromInt(1234),
            new UserLogin('test_user@domain.com'),
            new UserPassword('test_password')
        );
        $this->repository->save($registeredUser);

        $user = $this->repository->getByHash($registeredUser->hash());

        self::assertInstanceOf(RegisteredUser::class, $user);
    }

    /**
     * @test
     */
    public function shouldFailWhenUserByHashNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->repository->getByHash('not_found_user_hash');
    }

    /**
     * @test
     */
    public function shouldSaveUser(): void
    {
        $user = new RegisteredUser(
            UserId::fromInt(1),
            new UserLogin('test_user@domain.com'),
            new UserPassword('test_password'),
            true
        );
        $this->repository->save($user);

        self::assertTrue($this->repository->getById(UserId::fromInt(1))->isActive());
    }

    /**
     * Setup fixtures
     */
    protected function setUp(): void
    {
        InMemoryStorage::clear();

        $this->repository = new InMemoryRegisteredUserRepository();

        $this->repository->save(
            RegisteredUser::register(
                UserId::fromInt(1),
                new UserLogin('registered_user@domain.com'),
                new UserPassword('test_password')
            )
        );
    }
}