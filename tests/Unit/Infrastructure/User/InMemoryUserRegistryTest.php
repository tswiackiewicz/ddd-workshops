<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Exception\UserNotFoundException
};
use TSwiackiewicz\AwesomeApp\Infrastructure\{
    InMemoryStorage, User\InMemoryUserRegistry
};
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\UserRegistryException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class InMemoryUserRegistryTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User
 *
 * @coversDefaultClass InMemoryUserRegistry
 */
class InMemoryUserRegistryTest extends UserBaseTestCase
{
    /**
     * @var InMemoryUserRegistry
     */
    private $registry;

    /**
     * @test
     */
    public function shouldReturnTrueWhenUserExists(): void
    {
        self::assertTrue($this->registry->exists($this->login));
    }

    /**
     * @test
     */
    public function shouldReturnFalseWhenUserNotExists(): void
    {
        self::assertFalse($this->registry->exists('non_existent_user@domain.com'));
    }

    /**
     * @test
     */
    public function shouldFindUserByLogin(): void
    {
        $userId = $this->registry->getByLogin($this->login);

        self::assertEquals($this->getUserId(), $userId);
    }

    /**
     * @test
     */
    public function shouldFetchUserByLoginOnlyOnce(): void
    {
        $firstAttemptUser = $this->registry->getByLogin($this->login);

        $registry = new InMemoryUserRegistry();
        $secondAttemptUser = $registry->getByLogin($this->login);

        self::assertSame($firstAttemptUser, $secondAttemptUser);
    }

    /**
     * @test
     */
    public function shouldFailWhenUserByLoginNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->registry->getByLogin('non_existent_user@domain.com');
    }

    /**
     * @test
     */
    public function shouldFailWhenDataInStorageIsInvalidForUserFetchedByLogin(): void
    {
        $this->clearCache();
        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'id' => -1234,
                'uuid' => $this->getUserId()->getAggregateId(),
                'login' => $this->login,
                'hash' => $this->hash
            ]
        );

        $this->expectException(UserRegistryException::class);

        $this->registry->getByLogin($this->login);
    }

    /**
     * Clear cache
     */
    private function clearCache(): void
    {
        InMemoryStorage::clear();

        $identityMap = new \ReflectionProperty(InMemoryUserRegistry::class, 'identityMap');
        $identityMap->setAccessible(true);
        $identityMap->setValue(null, []);
    }

    /**
     * @test
     */
    public function shouldFindUserByHash(): void
    {
        $userId = $this->registry->getByHash($this->hash);

        self::assertEquals($this->getUserId(), $userId);
    }

    /**
     * @test
     */
    public function shouldFailWhenUserByHashNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->registry->getByHash('non_existent_user_hash');
    }

    /**
     * @test
     */
    public function shouldFailWhenDataInStorageIsInvalidForUserFetchedByHash(): void
    {
        $this->clearCache();
        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'id' => -1234,
                'uuid' => $this->getUserId()->getAggregateId(),
                'login' => $this->login,
                'hash' => $this->hash
            ]
        );

        $this->expectException(UserRegistryException::class);

        $this->registry->getByHash($this->hash);
    }

    /**
     * @test
     */
    public function shouldAddUserToRegistry(): void
    {
        $this->clearCache();

        /** @var UserId $userId */
        $userId = UserId::generate()->setId(1234);
        $this->registry->put('another.user@domain.com', $userId);

        self::assertEquals($userId, $this->registry->getByLogin('another.user@domain.com'));
    }

    /**
     * Setup fixtures
     */
    protected function setUp(): void
    {
        $this->clearCache();

        $this->registry = new InMemoryUserRegistry();

        InMemoryStorage::save(
            InMemoryStorage::TYPE_USER,
            [
                'id' => $this->userId,
                'uuid' => $this->getUserId()->getAggregateId(),
                'login' => $this->login,
                'hash' => $this->hash
            ]
        );
    }
}