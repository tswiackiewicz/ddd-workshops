<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\Event;

/**
 * EventBus should be a part of "DDD framework" or external component
 * To keep example simple, EB is delivered along with sample application (AwesomeApp)
 *
 * @package TSwiackiewicz\DDD\Event
 */
class EventBus
{
    /**
     * @var array
     */
    protected static $handlers = [];

    /**
     * @param Event $event
     */
    public static function publish(Event $event): void
    {
        $eventName = get_class($event);
        if (isset(self::$handlers[$eventName]) && self::$handlers[$eventName] instanceof EventHandler) {
            /** @var EventHandler $handler */
            $handler = self::$handlers[$eventName];
            $handler->handle($event);
        }
    }

    /**
     * @param string $eventName
     * @param EventHandler $handler
     */
    public static function subscribe(string $eventName, EventHandler $handler): void
    {
        self::$handlers[$eventName] = $handler;
    }
}