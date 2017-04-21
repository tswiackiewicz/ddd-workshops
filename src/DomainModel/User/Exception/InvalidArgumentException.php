<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\DomainModel\User\Exception;

/**
 * Class InvalidArgumentException
 * @package TSwiackiewicz\AwesomeApp\DomainModel\User\Exception
 */
class InvalidArgumentException extends \InvalidArgumentException implements UserDomainModelException
{

}