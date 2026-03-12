<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Application\User\Handler;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TSwiackiewicz\AwesomeApp\Application\User\Handler\UserEnabledEventHandler;
use TSwiackiewicz\AwesomeApp\SharedKernel\Exception\RuntimeException;
use TSwiackiewicz\AwesomeApp\Tests\UserBaseTestCase;

#[CoversClass(UserEnabledEventHandler::class)]
class UserEnabledEventHandlerTest extends UserBaseTestCase
{
    #[Test]
    public function shouldFailWhenHandledEventIsInvalid(): void
    {
        $this->expectException(RuntimeException::class);

        $handler = new UserEnabledEventHandler(
            $this->getUserNotifierMock()
        );
        $handler->handle(FakeUserEvent::create());
    }
}
