<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Exception;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

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
            sprintf('User (login = "%s") not found', $user),
            self::NOT_FOUND_ERROR_CODE
        );
    }

    /**
     * @param string $hash
     * @return static
     */
    public static function forHash(string $hash)
    {
        return new static(
            sprintf('User (hash = "%s") not found', $hash),
            self::NOT_FOUND_ERROR_CODE
        );
    }

    /**
     * @param UserId $userId
     * @return static
     */
    public static function forId(UserId $userId)
    {
        return new static(
            sprintf('User (user_id = %d) not found', $userId->getId()),
            self::NOT_FOUND_ERROR_CODE
        );
    }
}