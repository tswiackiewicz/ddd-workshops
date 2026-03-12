<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User\Password;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPasswordService;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

#[CoversClass(UserPasswordService::class)]
class UserPasswordServiceTest extends UserBaseTestCase
{
    #[Test]
    public function shouldCheckIfPasswordIsWeak(): void
    {
        $password = 'weak_password';

        $service = new UserPasswordService();

        self::assertTrue($service->isWeak($password));
        self::assertFalse($service->isStrong($password));
    }

    #[Test]
    public function shouldCheckIfPasswordIsStrong(): void
    {
        $password = 'StRoNg_PasSw0rD1';

        $service = new UserPasswordService();

        self::assertFalse($service->isWeak($password));
        self::assertTrue($service->isStrong($password));
        self::assertFalse($service->isVeryStrong($password));
    }

    #[Test]
    public function shouldCheckIfPasswordIsVeryStrong(): void
    {
        $password = 'VEEERY_StR0Ng_P@sSw0rD1!#';

        $service = new UserPasswordService();

        self::assertFalse($service->isWeak($password));
        self::assertTrue($service->isStrong($password));
        self::assertTrue($service->isVeryStrong($password));
    }

    #[Test]
    public function shouldGenerateStrongPassword(): void
    {
        $service = new UserPasswordService();
        $password = $service->generateStrongPassword();

        self::assertTrue($service->isVeryStrong($password));
    }
}
