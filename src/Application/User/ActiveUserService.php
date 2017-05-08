<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Command\{
    ChangePasswordCommand, DisableUserCommand, EnableUserCommand, RemoveUserCommand
};
use TSwiackiewicz\AwesomeApp\DomainModel\User\{
    ActiveUserRepository, Event\UserEnabledEvent, Event\UserRemovedEvent
};
use TSwiackiewicz\AwesomeApp\SharedKernel\{
    User\Exception\UserDomainModelException
};
use TSwiackiewicz\DDD\Event\EventBus;

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
     * ActiveUserService constructor.
     * @param ActiveUserRepository $activeUserRepository
     */
    public function __construct(ActiveUserRepository $activeUserRepository)
    {
        $this->repository = $activeUserRepository;
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

        $this->repository->save($user);

        EventBus::publish(
            new UserEnabledEvent(
                $user->getId(),
                (string)$user->getLogin()
            )
        );
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
     * Remove user
     *
     * @param RemoveUserCommand $command
     * @throws UserDomainModelException
     */
    public function remove(RemoveUserCommand $command): void
    {
        $user = $this->repository->getById($command->getUserId());

        $this->repository->remove($user->getId());

        EventBus::publish(
            new UserRemovedEvent(
                $user->getId(),
                (string)$user->getLogin()
            )
        );
    }
}