<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User;

use TSwiackiewicz\DDD\Event\EventBus;

/**
 * Class FakeEventBus
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Application\User
 */
class FakeEventBus extends EventBus
{
    /**
     * Clear registered event handlers
     */
    public static function clearRegisteredHandlers(): void
    {
        self::$handlers = [];
    }
}