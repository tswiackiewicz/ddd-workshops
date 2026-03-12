<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User\Command;

class ActivateUserCommand implements UserCommand
{
    public function __construct(private readonly string $hash)
    {
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
