<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\SharedKernel\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\UserId;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

#[CoversClass(UserId::class)]
class UserIdTest extends UserBaseTestCase
{
    #[Test]
    public function shouldCreateFromInt(): void
    {
        $userId = UserId::fromInt($this->userId);

        self::assertInstanceOf(UserId::class, $userId);
        self::assertFalse($userId->isNull());
        self::assertEquals($this->userId, $userId->getId());
    }

    #[Test]
    public function shouldCreateNullInstance(): void
    {
        $userId = UserId::nullInstance();

        self::assertInstanceOf(UserId::class, $userId);
        self::assertTrue($userId->isNull());
    }

    #[Test]
    public function shouldFailWhileCreationInvalidUserId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        UserId::fromInt(-1234);
    }
}
