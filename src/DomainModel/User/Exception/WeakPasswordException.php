<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Exception;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Class WeakPasswordException
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Exception
 */
class WeakPasswordException extends UserException
{
    const ERROR_CODE = self::WEAK_PASSWORD_ERROR_CODE;

    /**
     * @param UserId $userId
     * @return static
     */
    public static function forId(UserId $userId)
    {
        return new static(
            sprintf('Password for user (user_id = %d) is too weak', $userId->getId()),
            self::ERROR_CODE
        );
    }
}