<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    ActiveUser, Exception\UserNotFoundException, Password\UserPassword, RegisteredUser, User, UserFactory, UserLogin
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryStorage, User\InMemoryUserRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class InMemoryUserRepositoryTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User
 *
 * @@coversDefaultClass InMemoryUserRepository
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
        self::assertTrue($this->repository->exists('registered_user@domain.com'));
        self::assertTrue($this->repository->exists('active_enabled_user@domain.com'));
        self::assertTrue($this->repository->exists('active_disabled_user@domain.com'));
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
            UserId::fromInt(123),
            new UserLogin('test_user@domain.com'),
            new UserPassword('test_password')
        );
        $this->repository->save($registeredUser);

        $user = $this->repository->getRegisteredUserByHash($registeredUser->hash());

        self::assertInstanceOf(User::class, $user);
    }

    /**
     * @test
     */
    public function shouldFailWhenUserByHashNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->repository->getRegisteredUserByHash('not_found_user_hash');
    }

    /**
     * @test
     */
    public function shouldReturnUserByLogin(): void
    {
        $user = $this->repository->getActiveUserById(UserId::fromInt(2));

        self::assertInstanceOf(User::class, $user);
    }

    /**
     * @test
     */
    public function shouldFailWhenUserByLoginNotFoundForNonExistentUser(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->repository->getActiveUserById(UserId::fromInt(123));
    }

    /**
     * @test
     */
    public function shouldFailWhenUserByLoginNotFoundForInactiveUser(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->repository->getActiveUserById(UserId::fromInt(1));
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

        self::assertTrue($this->repository->exists('test_user@domain.com'));
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

        self::assertTrue($this->repository->exists('test_user@domain.com'));

        $this->repository->remove(UserId::fromInt(123));

        self::assertFalse($this->repository->exists('test_user@domain.com'));
    }

    /**
     * Setup fixtures
     */
    protected function setUp(): void
    {
        InMemoryStorage::clear();

        $this->repository = new InMemoryUserRepository(
            new UserFactory()
        );

        $this->repository->save(
            RegisteredUser::register(
                UserId::fromInt(1),
                new UserLogin('registered_user@domain.com'),
                new UserPassword('test_password')
            )
        );
        $this->repository->save(
            new ActiveUser(
                UserId::fromInt(2),
                new UserLogin('active_enabled_user@domain.com'),
                new UserPassword('test_password'),
                true
            )
        );
        $this->repository->save(
            new ActiveUser(
                UserId::fromInt(3),
                new UserLogin('active_disabled_user@domain.com'),
                new UserPassword('test_password'),
                false
            )
        );
    }
}