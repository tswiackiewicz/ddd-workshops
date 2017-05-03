<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Exception;

use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\UserDomainModelException;

/**
 * Generic exception for user's exceptions / failures
 * Example how to organize and maintain unique error codes within domain -
 * this abstract class contains whole error codes dictionary, inherited class
 * gives (public scope for const) access only to codes used by particular class
 * e.g. public const ERROR_CODE = self::NOT_FOUND_ERROR_CODE
 *
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Exception
 */
abstract class UserException extends \Exception implements UserDomainModelException
{
    protected const ALREADY_EXISTS_ERROR_CODE = 1001;
    protected const NOT_FOUND_ERROR_CODE = 1002;
}