<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class UserAuthenticatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var UserRepository|MockInterface
     */
    private $userRepository;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->userRepository = Mockery::mock(UserRepository::class);
    }

    /**
     * @return void
     */
    public function testAuthenticate(): void
    {
        $authenticator = $this->getUserAuthenticator();

        $this->userRepository->expects('findOneBy')->once()->andReturn(new User());

        $this->assertNull($authenticator->authenticate('test'));
        $this->assertEquals(new User(), $authenticator->authenticate('3edec063b94637eb1cc4395aa5abab91'));
    }

    /**
     * @return UserAuthenticator
     */
    private function getUserAuthenticator(): UserAuthenticator
    {
        return new UserAuthenticator(
            $this->userRepository
        );
    }
}
