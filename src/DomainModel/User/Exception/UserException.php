<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Exception;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\UserDomainModelException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;

/**
 * Generic exception for user's exceptions / failures
 * Example how to organize and maintain unique error codes within domain -
 * this abstract class contains whole error codes dictionary, inherited class
 * gives (public scope for const) access only to codes used by particular class
 * e.g. public const ERROR_CODE = self::NOT_FOUND_ERROR_CODE
 *
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Exception
 */
class UserException extends \Exception implements UserDomainModelException
{
    protected const ALREADY_EXISTS_ERROR_CODE = 1001;
    protected const NOT_FOUND_ERROR_CODE = 1002;
    protected const WEAK_PASSWORD_ERROR_CODE = 1003;
    protected const NEW_PASSWORD_EQUALS_WITH_CURRENT_PASSWORD_ERROR_CODE = 1004;

    protected const ALREADY_ACTIVATED = 1005;
    protected const ENABLE_NOT_ALLOWED = 1006;
    protected const DISABLE_NOT_ALLOWED = 1007;
    protected const PASSWORD_CHANGE_NOT_ALLOWED = 1008;

    /**
     * @param UserId $userId
     * @return UserException
     */
    public static function alreadyActivated(UserId $userId): UserException
    {
        return new static(
            sprintf('User (user_id = %d) already activated', $userId->getId()),
            self::ALREADY_ACTIVATED
        );
    }

    /**
     * @param UserId $userId
     * @return UserException
     */
    public static function enableNotAllowed(UserId $userId): UserException
    {
        return new static(
            sprintf('Only active disabled user can be enabled, user_id = %d', $userId->getId()),
            self::ENABLE_NOT_ALLOWED
        );
    }

    /**
     * @param UserId $userId
     * @return UserException
     */
    public static function disableNotAllowed(UserId $userId): UserException
    {
        return new static(
            sprintf('Only active enabled user can be disabled, user_id = %d', $userId->getId()),
            self::DISABLE_NOT_ALLOWED
        );
    }

    /**
     * @param UserId $userId
     * @return UserException
     */
    public static function passwordChangeNotAllowed(UserId $userId): UserException
    {
        return new static(
            sprintf('Only active enabled user can change password, user_id = %d', $userId->getId()),
            self::PASSWORD_CHANGE_NOT_ALLOWED
        );
    }
}