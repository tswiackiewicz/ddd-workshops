<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    Exception\UserNotFoundException, RegisteredUser, RegisteredUserRepository
};
use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryStorage;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\{
    Exception\InvalidArgumentException, Exception\UserRepositoryException, UserId
};

/**
 * Class InMemoryRegisteredUserRepository
 * @package TSwiackiewicz\AwesomeApp\Infrastructure\User
 */
class InMemoryRegisteredUserRepository implements RegisteredUserRepository
{
    /**
     * @var array
     */
    private static $identityMap = [];

    /**
     * @return UserId
     */
    public function nextIdentity(): UserId
    {
        return UserId::nullInstance();
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
     * @return RegisteredUser
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getById(UserId $id): RegisteredUser
    {
        if (isset(self::$identityMap[$id->getId()])) {
            return self::$identityMap[$id->getId()];
        }

        $user = InMemoryStorage::fetchById(InMemoryStorage::TYPE_USER, $id->getId());
        if ($user) {
            try {
                self::$identityMap[$id->getId()] = RegisteredUser::fromNative($user);

                return self::$identityMap[$id->getId()];
            } catch (InvalidArgumentException $exception) {
                throw UserRepositoryException::fromPrevious($exception);
            }
        }

        throw UserNotFoundException::forId($id);
    }

    /**
     * @param string $hash
     * @return RegisteredUser
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getByHash(string $hash): RegisteredUser
    {
        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);
        foreach ($users as $user) {
            if (!empty($user['id']) &&
                isset($user['hash']) && $hash === $user['hash'] &&
                (!isset($user['active']) || false === $user['active'])
            ) {
                try {
                    self::$identityMap[$user['id']] = RegisteredUser::fromNative($user);

                    return self::$identityMap[$user['id']];
                } catch (InvalidArgumentException $exception) {
                    throw UserRepositoryException::fromPrevious($exception);
                }
            }
        }

        throw UserNotFoundException::forHash($hash);
    }

    /**
     * @param RegisteredUser $user
     * @return UserId
     * @throws UserRepositoryException
     */
    public function save(RegisteredUser $user): UserId
    {
        if ($user->getId()->isNull()) {
            $userId = InMemoryStorage::nextIdentity(InMemoryStorage::TYPE_USER);
        } else {
            $userId = $user->getId()->getId();
        }

        $nativeUser = [
            'id' => $userId,
            'login' => (string)$user->getLogin(),
            'password' => (string)$user->getPassword(),
            'hash' => $user->hash(),
            'active' => $user->isActive(),
            'enabled' => true
        ];

        InMemoryStorage::save(InMemoryStorage::TYPE_USER, $nativeUser);

        try {
            self::$identityMap[$userId] = RegisteredUser::fromNative($nativeUser);

            return UserId::fromInt($nativeUser['id']);
        } catch (InvalidArgumentException $exception) {
            throw UserRepositoryException::fromPrevious($exception);
        }
    }
}