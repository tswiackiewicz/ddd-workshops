<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception;

/**
 * We would like to have ability to catch \InvalidArgumentException along with
 * others User's domain exceptions (UserDomainModelException),
 * that's why we overwrite \InvalidArgumentException with ours
 *
 * @package TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception
 */
class InvalidArgumentException extends \InvalidArgumentException implements UserDomainModelException
{

}