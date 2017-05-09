<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    ChangePasswordCommand, DisableUserCommand, EnableUserCommand, UnregisterUserCommand
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\ActiveUserRepository;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\UserDomainModelException;

/**
 * Class ActiveUserService
 * @package TSwiackiewicz\AwesomeApp\Application\User
 */
class ActiveUserService
{
    /**
     * @var CommandValidator
     */
    private $validator;

    /**
     * @var ActiveUserRepository
     */
    private $repository;

    /**
     * ActiveUserService constructor.
     * @param CommandValidator $validator
     * @param ActiveUserRepository $repository
     */
    public function __construct(CommandValidator $validator, ActiveUserRepository $repository)
    {
        $this->validator = $validator;
        $this->repository = $repository;
    }

    /**
     * Enable active user
     *
     * @param EnableUserCommand $command
     * @throws UserDomainModelException
     */
    public function enable(EnableUserCommand $command): void
    {
        $this->validator->validate($command);

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

    }

    /**
     * Change active user's password
     *
     * @param ChangePasswordCommand $command
     * @throws UserDomainModelException
     */
    public function changePassword(ChangePasswordCommand $command): void
    {

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
        $this->validator->validate($command);

        $user = $this->repository->getById($command->getUserId());
        $user->unregister();
    }
}