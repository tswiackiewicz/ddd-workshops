<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Domain\User\ValueObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TSwiackiewicz\AwesomeApp\Domain\User\ValueObject\UserPassword;
use TSwiackiewicz\AwesomeApp\SharedKernel\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\Tests\UserBaseTestCase;

#[CoversClass(UserPassword::class)]
class UserPasswordTest extends UserBaseTestCase
{
    #[Test]
    public function shouldCreateUserPassword(): void
    {
        $password = new UserPassword('password1234');

        self::assertInstanceOf(UserPassword::class, $password);
        self::assertEquals('password1234', $password->getPassword());
        self::assertEquals('password1234', (string)$password);
    }

    #[Test]
    #[DataProvider('getInvalidPasswordDataProvider')]
    public function shouldFailWhileCreationInvalidUserPassword(string $invalidPassword): void
    {
        $this->expectException(InvalidArgumentException::class);

        new UserPassword($invalidPassword);
    }
}
