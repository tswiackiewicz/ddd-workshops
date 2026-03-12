<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Handler;

use TSwiackiewicz\AwesomeApp\Application\User\Command\UnregisterUserCommand;
use TSwiackiewicz\AwesomeApp\Domain\User\Event\UserUnregisteredEvent;
use TSwiackiewicz\AwesomeApp\Domain\User\Repository\UserRepository;
use TSwiackiewicz\DDD\Event\EventBus;

final class UnregisterUserHandler
{
    public function __construct(
        private readonly UserRepository $repository
    ) {}

    public function __invoke(UnregisterUserCommand $command): void
    {
        $user = $this->repository->getById($command->getUserId());

        $this->repository->remove($user->getId());

        EventBus::publish(new UserUnregisteredEvent($user->getId()));
    }
}
