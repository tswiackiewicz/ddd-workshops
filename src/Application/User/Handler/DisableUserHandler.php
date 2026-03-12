<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Handler;

use TSwiackiewicz\AwesomeApp\Application\User\Command\DisableUserCommand;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserDisabledEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Repository\UserRepository;
use TSwiackiewicz\DDD\Event\EventBus;

final class DisableUserHandler
{
    public function __construct(
        private readonly UserRepository $repository
    ) {}

    public function __invoke(DisableUserCommand $command): void
    {
        $user = $this->repository->getById($command->getUserId());
        $user->disable();

        $this->repository->save($user);

        EventBus::publish(new UserDisabledEvent($user->getId()));
    }
}
