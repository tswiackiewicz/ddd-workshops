<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Exception;

use TSwiackiewicz\DDD\AggregateId;

/**
 * Class PasswordException
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Exception
 */
class PasswordException extends UserException
{
    const WEAK_PASSWORD = self::WEAK_PASSWORD_ERROR_CODE;
    const NEW_PASSWORD_EQUALS_WITH_CURRENT_PASSWORD = self::NEW_PASSWORD_EQUALS_WITH_CURRENT_PASSWORD_ERROR_CODE;

    /**
     * @param AggregateId $userId
     * @return static
     */
    public static function weakPassword(AggregateId $userId)
    {
        return new static(
            sprintf('Password for user (user_id = %d) is too weak', $userId->getId()),
            self::WEAK_PASSWORD
        );
    }

    /**
     * @param AggregateId $userId
     * @return static
     */
    public static function newPasswordEqualsWithCurrentPassword(AggregateId $userId)
    {
        return new static(
            sprintf('Changed password should be different from current password, user_id = %d', $userId->getId()),
            self::NEW_PASSWORD_EQUALS_WITH_CURRENT_PASSWORD
        );
    }
}