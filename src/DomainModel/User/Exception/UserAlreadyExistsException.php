<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Exception;

/**
 * Class UserAlreadyExistsException
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Exception
 */
class UserAlreadyExistsException extends UserException
{
    /**
     * @param string $user
     * @return static
     */
    public static function forUser(string $user)
    {
        return new static(
            sprintf('User `%s` already exists', $user),
            self::ALREADY_EXISTS_ERROR_CODE
        );
    }
}