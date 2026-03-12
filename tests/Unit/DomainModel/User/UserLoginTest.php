<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TSwiackiewicz\AwesomeApp\SharedKernel\User\Exception\InvalidArgumentException;
use TSwiackiewicz\AwesomeApp\DomainModel\User\UserLogin;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

#[CoversClass(UserLogin::class)]
class UserLoginTest extends UserBaseTestCase
{
    #[Test]
    public function shouldCreateUserLogin(): void
    {
        $login = new UserLogin('test@domain.com');

        self::assertInstanceOf(UserLogin::class, $login);
        self::assertEquals('test@domain.com', $login->getLogin());
        self::assertEquals('test@domain.com', (string)$login);
    }

    #[Test]
    #[DataProvider('getInvalidLoginDataProvider')]
    public function shouldFailWhileCreationInvalidUserLogin(string $invalidLogin): void
    {
        $this->expectException(InvalidArgumentException::class);

        new UserLogin($invalidLogin);
    }
}
