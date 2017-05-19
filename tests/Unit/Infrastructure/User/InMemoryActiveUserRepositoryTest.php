<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    ActiveUser, Exception\UserNotFoundException, Password\UserPassword, User, UserLogin
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryStorage, User\InMemoryActiveUserRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\UserRepositoryException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class InMemoryActiveUserRepositoryTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User
 *
 * @@coversDefaultClass InMemoryActiveUserRepository
 * @runTestsInSeparateProcesses
 */
class InMemoryActiveUserRepositoryTest extends UserBaseTestCase
{
    /**
     * @var InMemoryActiveUserRepository
     */
    private $repository;

    /**
     * @test
     */
    public function shouldReturnUserById(): void
    {
        $user = $this->repository->getById(
            UserId::fromInt(1)
        );

        self::assertInstanceOf(User::class, $user);
    }

    /**
     * @test
     */
    public function shouldFetchUserByIdFromStorageOnlyOnce(): void
    {
        $firstAttemptUser = $this->repository->getById(
            UserId::fromInt(1)
        );

        $repository = new InMemoryActiveUserRepository();
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
    public function shouldSaveUser(): void
    {
        $user = new ActiveUser(
            UserId::fromInt(1),
            new UserLogin('test_user@domain.com'),
            new UserPassword('test_password'),
            true
        );
        $this->repository->save($user);

        self::assertEquals(
            'test_user@domain.com',
            (string)$this->repository->getById(UserId::fromInt(1))->getLogin()
        );
    }

    /**
     * @test
     */
    public function shouldRemoveUserById(): void
    {
        $user = new ActiveUser(
            UserId::fromInt(123),
            new UserLogin('test_user@domain.com'),
            new UserPassword('test_password'),
            true
        );
        $this->repository->save($user);

        self::assertEquals(
            'test_user@domain.com',
            (string)$this->repository->getById(UserId::fromInt(123))->getLogin()
        );

        $this->repository->remove(UserId::fromInt(123));

        try {
            $this->repository->getById(UserId::fromInt(123));
        } catch (UserNotFoundException $exception) {
            // user not found
        }
    }

    /**
     * Setup fixtures
     */
    protected function setUp(): void
    {
        InMemoryStorage::clear();

        $this->repository = new InMemoryActiveUserRepository();

        $this->repository->save(
            new ActiveUser(
                UserId::fromInt(1),
                new UserLogin('active_enabled_user@domain.com'),
                new UserPassword('test_password'),
                true
            )
        );
        $this->repository->save(
            new ActiveUser(
                UserId::fromInt(2),
                new UserLogin('active_disabled_user@domain.com'),
                new UserPassword('test_password'),
                false
            )
        );
    }
}