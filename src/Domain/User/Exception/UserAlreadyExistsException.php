<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Domain\User\Exception;

class UserAlreadyExistsException extends UserException
{
    public static function forUser(string $user)
    {
        return new static(
            sprintf('User `%s` already exists', $user),
            self::ALREADY_EXISTS_ERROR_CODE
        );
    }
}
