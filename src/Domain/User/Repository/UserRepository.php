<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Domain\User\Repository;

use TSwiackiewicz\AwesomeApp\Domain\User\Entity\User;
use TSwiackiewicz\AwesomeApp\Domain\User\Exception\UserNotFoundException;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserId;
use TSwiackiewicz\AwesomeApp\SharedKernel\Exception\UserRepositoryException;

interface UserRepository
{
    public function nextIdentity(): UserId;

    public function exists(string $login): bool;

    public function getById(UserId $id): User;

    public function getByHash(string $hash): User;

    public function save(User $user): UserId;

    public function remove(UserId $id): void;
}
