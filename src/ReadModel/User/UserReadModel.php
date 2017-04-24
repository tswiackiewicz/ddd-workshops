<?php

namespace TSwiackiewicz\AwesomeApp\ReadModel\User;

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
     * @param int $id
     * @return null|UserDTO
     */
    public function findById(int $id): ?UserDTO
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
    public function getAllUsers() : array
    {
        return $this->repository->getAllUsers();
    }
}