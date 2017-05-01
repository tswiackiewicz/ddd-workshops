<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\SharedKernel\Event;

use TSwiackiewicz\AwesomeApp\SharedKernel\Event\EventBus;

/**
 * Class FakeEventBus
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\SharedKernel\Event
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