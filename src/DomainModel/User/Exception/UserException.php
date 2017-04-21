<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Exception;

/**
 * Class UserException
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Exception
 */
abstract class UserException extends \Exception implements UserDomainModelException
{
    protected const ALREADY_EXISTS_ERROR_CODE = 1001;
    protected const NOT_FOUND_ERROR_CODE = 1002;
}