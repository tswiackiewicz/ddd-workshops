<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Exception;

/**
 * Class UserNotFoundException
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Exception
 */
class UserNotFoundException extends UserException
{
    /**
     * @param string $user
     * @return static
     */
    public static function forUser(string $user)
    {
        return new static(
            sprintf('User `%s` not found', $user),
            self::NOT_FOUND_ERROR_CODE
        );
    }
}