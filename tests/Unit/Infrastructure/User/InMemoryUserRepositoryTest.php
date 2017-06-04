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
        self::assertTrue($this->repository->exists($this->login));
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
            UserId::fromInt($this->userId)
        );

        self::assertInstanceOf(User::class, $user);
    }

    /**
     * @test
     */
    public function shouldFetchUserByIdFromStorageOnlyOnce(): void
    {
        $firstAttemptUser = $this->repository->getById(
            UserId::fromInt($this->userId)
        );

        $repository = new InMemoryUserRepository();
        $secondAttemptUser = $repository->getById(
            UserId::fromInt($this->userId)
        );

        self::assertSame($firstAttemptUser, $secondAttemptUser);
    }

    /**
     * @test
     */
    public function shouldFailWhenDataInStorageIsInvalidForGivenUserId(): void
    {
        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'id' => $this->userId,
                'login' => '',
                'password' => '',
                'hash' => '',
                'active' => true,
                'enabled' => true
            ]
        );
        $this->expectException(UserRepositoryException::class);

        $this->repository->getById(
            UserId::fromInt($this->userId)
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
    public function shouldReturnUserByLogin(): void
    {
        $user = $this->repository->getByLogin($this->login);

        self::assertEquals($this->login, (string)$user->getLogin());
    }

    /**
     * @test
     */
    public function shouldFailWhenDataInStorageIsInvalidForGivenLogin(): void
    {
        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'id' => $this->userId,
                'login' => $this->login,
                'password' => '',
                'hash' => '',
                'active' => true,
                'enabled' => true
            ]
        );
        $this->expectException(UserRepositoryException::class);

        $this->repository->getByLogin($this->login);
    }

    /**
     * @test
     */
    public function shouldFailWhenUserByLoginNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->repository->getByLogin('not_found_user_login');
    }

    /**
     * @test
     */
    public function shouldReturnUserByHash(): void
    {
        $user = $this->repository->getByHash($this->hash);

        self::assertEquals($this->userId, $user->getId()->getId());
    }

    /**
     * @test
     */
    public function shouldFailWhenDataInStorageIsInvalidForGivenHash(): void
    {
        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'id' => $this->userId,
                'login' => '',
                'password' => '',
                'hash' => $this->hash,
                'active' => true,
                'enabled' => true
            ]
        );
        $this->expectException(UserRepositoryException::class);

        $this->repository->getByHash($this->hash);
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
        $user = new User(
            UserId::fromInt($this->userId),
            new UserLogin($this->login),
            new UserPassword($this->password),
            true,
            true
        );
        $this->repository->save($user);

        self::assertEquals(
            $this->login,
            (string)$this->repository->getById(UserId::fromInt($this->userId))->getLogin()
        );
    }

    /**
     * @test
     */
    public function shouldRemoveUserById(): void
    {
        self::assertEquals(
            $this->login,
            (string)$this->repository->getById(UserId::fromInt($this->userId))->getLogin()
        );

        $this->repository->remove(UserId::fromInt($this->userId));

        try {
            $this->repository->getById(UserId::fromInt($this->userId));
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
                UserId::fromInt($this->userId),
                new UserLogin($this->login),
                new UserPassword($this->password),
                true,
                true
            )
        );

        $identityMap = new \ReflectionProperty(InMemoryUserRepository::class, 'identityMap');
        $identityMap->setAccessible(true);
        $identityMap->setValue(null, []);
    }
}