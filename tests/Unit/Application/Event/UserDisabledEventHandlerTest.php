<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\Application\Event;

use TSwiackiewicz\AwesomeApp\Application\User\Event\UserDisabledEventHandler;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\RuntimeException;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class UserDisabledEventHandlerTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\Application\Event
 *
 * @coversDefaultClass UserDisabledEventHandler
 */
class UserDisabledEventHandlerTest extends UserBaseTestCase
{
    /**
     * @test
     */
    public function shouldFailWhenHandledEventIsInvalid(): void
    {
        $this->expectException(RuntimeException::class);

        $handler = new UserDisabledEventHandler(
            $this->getUserNotifierMock()
        );
        $handler->handle(FakeUserEvent::create());
    }
}