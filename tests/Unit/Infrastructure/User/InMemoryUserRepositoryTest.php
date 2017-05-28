<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Exception\UserNotFoundException, Password\UserPassword, User, UserLogin
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryStorage, User\InMemoryUserRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\UserRepositoryException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class InMemoryUserRepositoryTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User
 *
 * @@coversDefaultClass InMemoryUserRepository
 * @runTestsInSeparateProcesses
 */
class InMemoryUserRepositoryTest extends UserBaseTestCase
{
    /**
     * @var InMemoryUserRepository
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
        self::assertTrue($this->repository->exists('active_enabled_user@domain.com'));
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

        $repository = new InMemoryUserRepository();
        $secondAttemptUser = $repository->getById(
            UserId::fromInt(1)
        );

        self::assertSame($firstAttemptUser, $secondAttemptUser);
    }

    /**
     * @test
     */
    public function shouldReturnUserByHash(): void
    {
        $this->markTestSkipped('TODO: fix shouldReturnUserByHash() test');

        $u = new User(
            UserId::fromInt(1),
            new UserLogin('active_enabled_user@domain.com'),
            new UserPassword('test_password'),
            true,
            true
        );
        $user = $this->repository->getByHash($u->hash());

        self::assertInstanceOf(User::class, $user);
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
        $user = new User(
            UserId::fromInt(1),
            new UserLogin('test_user@domain.com'),
            new UserPassword('test_password'),
            true,
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
        $user = new User(
            UserId::fromInt(123),
            new UserLogin('test_user@domain.com'),
            new UserPassword('test_password'),
            true,
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

        $this->repository = new InMemoryUserRepository();

        $this->repository->save(
            new User(
                UserId::fromInt(1),
                new UserLogin('active_enabled_user@domain.com'),
                new UserPassword('test_password'),
                true,
                true
            )
        );
        $this->repository->save(
            new User(
                UserId::fromInt(2),
                new UserLogin('active_disabled_user@domain.com'),
                new UserPassword('test_password'),
                true,
                false
            )
        );
    }
}