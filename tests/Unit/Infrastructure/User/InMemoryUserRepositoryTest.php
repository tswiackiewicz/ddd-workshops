<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Exception\UserNotFoundException, Password\UserPassword, User, UserLogin
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryStorage, User\InMemoryUserRepository
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\UserRepositoryException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

#[CoversClass(InMemoryUserRepository::class)]
#[RunTestsInSeparateProcesses]
class InMemoryUserRepositoryTest extends UserBaseTestCase
{
    private InMemoryUserRepository $repository;

    #[Test]
    public function shouldReturnNextIdentity(): void
    {
        $userId = $this->repository->nextIdentity();

        self::assertInstanceOf(UserId::class, $userId);
    }

    #[Test]
    public function shouldReturnTrueWhenUserExists(): void
    {
        self::assertTrue($this->repository->exists($this->login));
    }

    #[Test]
    public function shouldReturnFalseWhenUserNotExists(): void
    {
        self::assertFalse($this->repository->exists('non_existent_user'));
    }

    #[Test]
    public function shouldReturnUserById(): void
    {
        $this->clearIdentityMap();

        $user = $this->repository->getById(
            UserId::fromInt($this->userId)
        );

        self::assertInstanceOf(User::class, $user);
    }

    private function clearIdentityMap(): void
    {
        $identityMap = new \ReflectionProperty(InMemoryUserRepository::class, 'identityMap');
        $identityMap->setValue(null, []);
    }

    #[Test]
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

    #[Test]
    public function shouldFailWhenInvalidStorageDataForUserById(): void
    {
        $this->clearIdentityMap();

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

    #[Test]
    public function shouldFailWhenUserByIdNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->repository->getById(
            UserId::fromInt(12345)
        );
    }

    #[Test]
    public function shouldReturnUserByHash(): void
    {
        $user = $this->repository->getByHash($this->hash);

        self::assertInstanceOf(User::class, $user);
    }

    #[Test]
    public function shouldFailWhenInvalidStorageDataForUserByHash(): void
    {
        $this->clearIdentityMap();

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

    #[Test]
    public function shouldFailWhenUserByHashNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->repository->getByHash('not_found_user_hash');
    }

    #[Test]
    public function shouldSaveUser(): void
    {
        $user = new User(
            UserId::fromInt(1),
            new UserLogin('test_user@domain.com'),
            new UserPassword('test_password'),
            true,
            false
        );
        $this->repository->save($user);

        self::assertTrue($this->repository->getById(UserId::fromInt(1))->isActive());
    }

    #[Test]
    public function shouldSaveUserWithNullId(): void
    {
        InMemoryStorage::nextIdentity(InMemoryStorage::TYPE_USER);

        $user = new User(
            UserId::nullInstance(),
            new UserLogin('test_user@domain.com'),
            new UserPassword('test_password'),
            true,
            false
        );
        $userId = $this->repository->save($user);

        self::assertTrue($this->repository->getById($userId)->isActive());
    }

    #[Test]
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

    protected function setUp(): void
    {
        InMemoryStorage::clear();

        $this->repository = new InMemoryUserRepository();
        $this->repository->save(
            User::register(
                UserId::fromInt($this->userId),
                new UserLogin($this->login),
                new UserPassword($this->password)
            )
        );
    }
}
