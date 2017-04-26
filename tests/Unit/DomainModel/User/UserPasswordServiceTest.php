<?php
declare(strict_types=1);

namespace TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User;

use TSwiackiewicz\AwesomeApp\DomainModel\User\Password\UserPasswordService;
use TSwiackiewicz\AwesomeApp\Tests\Unit\UserBaseTestCase;

/**
 * Class UserPasswordServiceTest
 * @package TSwiackiewicz\AwesomeApp\Tests\Unit\DomainModel\User
 */
class UserPasswordServiceTest extends UserBaseTestCase
{
    /**
     * @test
     */
    public function shouldCheckIfPasswordIsWeak(): void
    {
        $password = 'weak_password';

        $service = new UserPasswordService();

        self::assertTrue($service->isWeak($password));
        self::assertFalse($service->isStrong($password));
    }

    /**
     * @test
     */
    public function shouldCheckIfPasswordIsStrong(): void
    {
        $password = 'StRoNg_PasSw0rD1';

        $service = new UserPasswordService();

        self::assertFalse($service->isWeak($password));
        self::assertTrue($service->isStrong($password));
        self::assertFalse($service->isVeryStrong($password));
    }

    /**
     * @test
     */
    public function shouldCheckIfPasswordIsVeryStrong(): void
    {
        $password = 'VEEERY_StR0Ng_P@sSw0rD1!#';

        $service = new UserPasswordService();

        self::assertFalse($service->isWeak($password));
        self::assertTrue($service->isStrong($password));
        self::assertTrue($service->isVeryStrong($password));
    }

    /**
     * @test
     */
    public function shouldGenerateStrongPassword(): void
    {
        $service = new UserPasswordService();
        $password = $service->generateStrongPassword();

        self::assertTrue($service->isVeryStrong($password));
    }
}