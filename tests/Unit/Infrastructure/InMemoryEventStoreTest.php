<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure;

use PHPUnit\Framework\TestCase;
use TSwiackiewicz\AwesomeApp\Infrastructure\InMemoryEventStore;
use TSwiackiewicz\DDD\AggregateId;
use TSwiackiewicz\DDD\Event\Event;

/**
 * Class InMemoryEventStoreTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Infrastructure\User
 *
 * @@coversDefaultClass InMemoryEventStore
 */
class InMemoryEventStoreTest extends TestCase
{
    /**
     * @var AggregateId
     */
    private $id;

    /**
     * @var Event[]
     */
    private $events;

    /**
     * @test
     */
    public function shouldAppendEvents(): void
    {
        $store = new InMemoryEventStore();

        /** @var Event $event */
        foreach ($this->events as $event) {
            $store->append($this->id, $event);
        }

        $events = $store->load($this->id);

        self::assertCount(count($this->events), $events);
    }

    /**
     * @test
     * @depends shouldAppendEvents
     */
    public function shouldLoadAppendedEvents(): void
    {
        $store = new InMemoryEventStore();

        $events = $store->load($this->id);
        self::assertCount(count($this->events), $events);
    }

    /**
     * @test
     * @depends shouldLoadAppendedEvents
     */
    public function shouldNotLoadEventsIfNotAppended(): void
    {
        InMemoryEventStore::clear();

        $store = new InMemoryEventStore();
        $events = $store->load($this->id);

        self::assertEquals([], $events);
    }

    /**
     * Setup fixtures
     */
    protected function setUp(): void
    {
        $this->id = FakeAggregateId::fromInt(1234);
        $this->events = array_fill(0, 10, new FakeDomainEvent($this->id));
    }
}