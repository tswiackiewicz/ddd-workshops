<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    ChangePasswordCommand, DisableUserCommand, EnableUserCommand, UnregisterUserCommand
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\ActiveUserRepository;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Exception\PasswordException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPasswordService;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\UserDomainModelException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\ValidationException;

/**
 * Class ActiveUserService
 * @package TSwiackiewicz\AwesomeApp\Application\User
 */
class ActiveUserService
{
    /**
     * @var ActiveUserRepository
     */
    private $repository;

    /**
     * @var UserPasswordService
     */
    private $passwordService;

    /**
     * ActiveUserService constructor.
     * @param ActiveUserRepository $repository
     * @param UserPasswordService $passwordService
     */
    public function __construct(
        ActiveUserRepository $repository,
        UserPasswordService $passwordService
    )
    {
        $this->repository = $repository;
        $this->passwordService = $passwordService;
    }

    /**
     * Enable active user
     *
     * @param EnableUserCommand $command
     * @throws UserDomainModelException
     */
    public function enable(EnableUserCommand $command): void
    {
        $user = $this->repository->getById($command->getUserId());
        $user->enable();
    }

    /**
     * Disable active user
     *
     * @param DisableUserCommand $command
     * @throws UserDomainModelException
     */
    public function disable(DisableUserCommand $command): void
    {
        $user = $this->repository->getById($command->getUserId());
        $user->disable();
    }

    /**
     * Change active user's password
     * Example usage of domain service (UserPasswordService)
     *
     * @param ChangePasswordCommand $command
     * @throws UserDomainModelException
     * @throws ValidationException
     */
    public function changePassword(ChangePasswordCommand $command): void
    {
        if ($this->passwordService->isWeak((string)$command->getPassword())) {
            throw PasswordException::weakPassword($command->getUserId());
        }

        $user = $this->repository->getById($command->getUserId());
        $user->changePassword($command->getPassword());
    }

    /**
     * Unregister user
     *
     * @see http://udidahan.com/2009/09/01/dont-delete-just-dont/
     * @param UnregisterUserCommand $command
     * @throws UserDomainModelException
     */
    public function unregister(UnregisterUserCommand $command): void
    {
        $user = $this->repository->getById($command->getUserId());
        $user->unregister();
    }
}