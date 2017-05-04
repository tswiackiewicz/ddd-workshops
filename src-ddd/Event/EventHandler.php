<?php
declare(strict_types=1);

namespace TSwiackiewicz\DDD\Event;

/**
 * Interface EventHandler
 * @package TSwiackiewicz\DDD\Event
 */
interface EventHandler
{
    /**
     * @param Event $event
     */
    public function handle(Event $event): void;
}