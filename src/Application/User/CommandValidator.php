<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Application\User;

use TSwiackiewicz\AwesomeApp\Application\User\Command\UserCommand;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\ValidationException;

class CommandValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(UserCommand $command): void
    {
        // TODO: command validation rules
    }
}
