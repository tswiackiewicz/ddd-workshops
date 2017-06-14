<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserRegistry;
use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryStorage;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\UserRegistryException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\DDD\AggregateId;

/**
 * Class InMemoryUserRegistry
 * @package TSwiackiewicz\AwesomeApp\Infrastructure\User
 */
class InMemoryUserRegistry implements UserRegistry
{
    /**
     * @var array
     */
    private static $identityMap = [];

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
     * @param string $login
     * @return UserId
     * @throws UserRegistryException
     * @throws UserNotFoundException
     */
    public function getByLogin(string $login): UserId
    {
        if (isset(self::$identityMap[$login])) {
            return self::$identityMap[$login];
        }

        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);
        foreach ($users as $user) {
            if (!empty($user['uuid']) && !empty($user['id']) && isset($user['login']) && $login === $user['login']) {
                try {
                    self::$identityMap[$login] = UserId::fromString($user['uuid'])->setId($user['id']);

                    return self::$identityMap[$login];
                } catch (InvalidArgumentException $exception) {
                    throw UserRegistryException::fromPrevious($exception);
                }
            }
        }

        throw UserNotFoundException::forUser($login);
    }

    /**
     * @param string $hash
     * @return UserId|AggregateId
     * @throws UserRegistryException
     * @throws UserNotFoundException
     */
    public function getByHash(string $hash): UserId
    {
        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);
        foreach ($users as $user) {
            if (!empty($user['uuid']) && !empty($user['id']) && isset($user['hash']) && $hash === $user['hash']) {
                try {
                    return UserId::fromString($user['uuid'])->setId($user['id']);
                } catch (InvalidArgumentException $exception) {
                    throw UserRegistryException::fromPrevious($exception);
                }
            }
        }

        throw UserNotFoundException::forHash($hash);
    }

    /**
     * @param string $login
     * @param UserId $id
     */
    public function put(string $login, UserId $id): void
    {
        self::$identityMap[$login] = $id;
    }
}