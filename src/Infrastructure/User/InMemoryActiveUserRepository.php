<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    ActiveUser, ActiveUserRepository, Exception\UserNotFoundException
};
use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryStorage;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\{
    Exception\InvalidArgumentException, Exception\UserRepositoryException, UserId
};

/**
 * Class InMemoryActiveUserRepository
 * @package TSwiackiewicz\AwesomeApp\Infrastructure\User
 */
class InMemoryActiveUserRepository implements ActiveUserRepository
{
    /**
     * @var array
     */
    private static $identityMap = [];

    /**
     * @param UserId $id
     * @return ActiveUser
     * @throws UserRepositoryException
     * @throws UserNotFoundException
     */
    public function getById(UserId $id): ActiveUser
    {
        if (isset(self::$identityMap[$id->getId()])) {
            return self::$identityMap[$id->getId()];
        }

        $user = InMemoryStorage::fetchById(InMemoryStorage::TYPE_USER, $id->getId());
        if (isset($user['active']) && true === $user['active']) {
            try {
                self::$identityMap[$id->getId()] = ActiveUser::fromNative($user);

                return self::$identityMap[$id->getId()];
            } catch (InvalidArgumentException $exception) {
                throw UserRepositoryException::fromPrevious($exception);
            }
        }

        throw UserNotFoundException::forId($id);
    }

    /**
     * @param ActiveUser $user
     * @throws UserRepositoryException
     */
    public function save(ActiveUser $user): void
    {
        $nativeUser = [
            'id' => $user->getId()->getId(),
            'login' => (string)$user->getLogin(),
            'password' => (string)$user->getPassword(),
            'hash' => $user->hash(),
            'active' => true,
            'enabled' => $user->isEnabled()
        ];

        InMemoryStorage::save(InMemoryStorage::TYPE_USER, $nativeUser);

        try {
            self::$identityMap[$nativeUser['id']] = ActiveUser::fromNative($nativeUser);
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