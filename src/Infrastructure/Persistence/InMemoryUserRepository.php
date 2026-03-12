<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Infrastructure\Persistence;

use TSwiackiewicz\AwesomeApp\Domain\User\Entity\User;
use TSwiackiewicz\AwesomeApp\Domain\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\Domain\User\Repository\UserRepository;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;
use TSwiackiewicz\AwesomeApp\SharedKernel\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\Exception\UserRepositoryException;

class InMemoryUserRepository implements UserRepository
{
    private static array $identityMap = [];

    public function nextIdentity(): UserId
    {
        return UserId::nullInstance();
    }

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

    public function getById(UserId $id): User
    {
        if (isset(self::$identityMap[$id->getId()])) {
            return self::$identityMap[$id->getId()];
        }

        $user = InMemoryStorage::fetchById(InMemoryStorage::TYPE_USER, $id->getId());
        if (!empty($user['id'])) {
            try {
                self::$identityMap[$user['id']] = User::fromNative($user);

                return self::$identityMap[$user['id']];
            } catch (InvalidArgumentException $exception) {
                throw UserRepositoryException::fromPrevious($exception);
            }
        }

        throw UserNotFoundException::forId($id);
    }

    public function getByHash(string $hash): User
    {
        $users = InMemoryStorage::fetchAll(InMemoryStorage::TYPE_USER);
        foreach ($users as $user) {
            if (!empty($user['id']) && isset($user['hash']) && $hash === $user['hash']) {
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

    public function save(User $user): UserId
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
            'status' => $user->getStatus()->value,
            'active' => $user->isActive(),
            'enabled' => $user->isEnabled()
        ];

        InMemoryStorage::save(InMemoryStorage::TYPE_USER, $nativeUser);

        try {
            self::$identityMap[$userId] = User::fromNative($nativeUser);

            return UserId::fromInt($nativeUser['id']);
        } catch (InvalidArgumentException $exception) {
            throw UserRepositoryException::fromPrevious($exception);
        }
    }

    public function remove(UserId $id): void
    {
        InMemoryStorage::removeById(InMemoryStorage::TYPE_USER, $id->getId());
        unset(self::$identityMap[$id->getId()]);
    }
}
