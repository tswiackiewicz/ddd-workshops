<?php

namespace TSwiackiewicz\AwesomeApp\ReadModel\User;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class UserReadModel
 * @package TSwiackiewicz\AwesomeApp\ReadModel\User
 */
class UserReadModel
{
    /**
     * @var UserReadModelRepository
     */
    private $repository;

    /**
     * UserReadModel constructor.
     * @param UserReadModelRepository $repository
     */
    public function __construct(UserReadModelRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param UserId $id
     * @return null|UserDTO
     */
    public function findById(UserId $id): ?UserDTO
    {
        return $this->repository->findById($id);
    }

    /**
     * @param UserQuery $query
     * @return UserDTO[]
     */
    public function findByQuery(UserQuery $query): array
    {
        return $this->repository->findByQuery($query);
    }

    /**
     * @return UserDTO[]
     */
    public function getAllUsers(): array
    {
        return $this->repository->getAllUsers();
    }
}