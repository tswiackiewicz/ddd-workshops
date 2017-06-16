<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Exception\UserNotFoundException, User, UserRepository
};
use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryStorage;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\{
    Exception\InvalidArgumentException, Exception\UserRepositoryException, UserId
};
use TSwiackiewicz\DDD\AggregateId;

/**
 * Class InMemoryUserRepository
 * @package TSwiackiewicz\AwesomeApp\Infrastructure\User
 */
class InMemoryUserRepository implements UserRepository
{
    /**
     * @var array
     */
    private static $identityMap = [];

    /**
     * @return UserId|AggregateId
     */
    public function nextIdentity(): UserId
    {
        return UserId::generate();
    }

    /**
     * @param string $login
     * @return bool
     */
    public function exists(string $login): bool
    {
        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);
        foreach ($users as $user) {
            if (isset($user['login']) && $login === $user['login']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param UserId $id
     * @return User
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getById(UserId $id): User
    {
        if (isset(self::$identityMap[$id->getId()])) {
            return self::$identityMap[$id->getId()];
        }

        $user = InMemoryStorage::fetchById(InMemoryStorage::TYPE_USER, $id->getId());
        if (!empty($user['uuid']) && !empty($user['id']) && $id->getId() === $user['id']) {
            try {
                self::$identityMap[$user['id']] = User::fromNative($user);

                return self::$identityMap[$user['id']];
            } catch (InvalidArgumentException $exception) {
                throw UserRepositoryException::fromPrevious($exception);
            }
        }

        throw UserNotFoundException::forId($id);
    }

    /**
     * @param string $login
     * @return User
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getByLogin(string $login): User
    {
        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);
        foreach ($users as $user) {
            if (!empty($user['uuid']) && !empty($user['id']) && isset($user['login']) && $login === $user['login']) {
                try {
                    self::$identityMap[$user['id']] = User::fromNative($user);

                    return self::$identityMap[$user['id']];
                } catch (InvalidArgumentException $exception) {
                    throw UserRepositoryException::fromPrevious($exception);
                }
            }
        }

        throw UserNotFoundException::forUser($login);
    }

    /**
     * @param string $hash
     * @return User
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getByHash(string $hash): User
    {
        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);
        foreach ($users as $user) {
            if (!empty($user['uuid']) && !empty($user['id']) && isset($user['hash']) && $hash === $user['hash']) {
                try {
                    self::$identityMap[$user['id']] = User::fromNative($user);

                    return self::$identityMap[$user['id']];
                } catch (InvalidArgumentException $exception) {
                    throw UserRepositoryException::fromPrevious($exception);
                }
            }
        }

        throw UserNotFoundException::forHash($hash);
    }

    /**
     * @param User $user
     * @throws UserRepositoryException
     */
    public function save(User $user): void
    {
        $userId = $user->getId()->setId(InMemoryStorage::nextIdentity(InMemoryStorage::TYPE_USER));

        $nativeUser = [
            'uuid' => $userId->getAggregateId(),
            'id' => $userId->getId(),
            'login' => (string)$user->getLogin(),
            'password' => (string)$user->getPassword(),
            'hash' => $user->hash(),
            'active' => $user->isActive(),
            'enabled' => $user->isEnabled()
        ];

        InMemoryStorage::save(InMemoryStorage::TYPE_USER, $nativeUser);

        try {
            self::$identityMap[$nativeUser['id']] = User::fromNative($nativeUser);
        } catch (InvalidArgumentException $exception) {
            throw UserRepositoryException::fromPrevious($exception);
        }
    }

    /**
     * @param UserId $id
     */
    public function remove(UserId $id): void
    {
        InMemoryStorage::removeById(InMemoryStorage::TYPE_USER, $id->getId());
        unset(self::$identityMap[$id->getId()]);
    }
}