<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception;

/**
 * Class InvalidArgumentException
 * @package TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception
 */
class InvalidArgumentException extends \InvalidArgumentException implements UserDomainModelException
{

}