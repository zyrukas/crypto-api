<?php

namespace App\Tests\Unit\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\TokenAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenAuthenticatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var EntityManagerInterface|MockInterface
     */
    private $entityManager;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
    }

    /**
     * @return void
     */
    public function testSupports(): void
    {
        $headerBagExternal = Mockery::mock(HeaderBag::class);
        $headerBagExternal->shouldReceive('has')->once()->andReturn(true);

        $request = Mockery::mock(Request::class);
        $request->headers = $headerBagExternal;

        $authenticator = $this->getTokenAuthenticator();
        $this->assertTrue($authenticator->supports($request));
    }

    /**
     * @param string $data
     * @param string $expect
     *
     * @return void
     *
     * @dataProvider getCredentialsDataProvider
     */
    public function testGetCredentials(string $data, string $expect): void
    {
        $headerBagExternal = Mockery::mock(HeaderBag::class);
        $headerBagExternal->shouldReceive('get')->once()->andReturn($data);

        $request = Mockery::mock(Request::class);
        $request->headers = $headerBagExternal;

        $authenticator = $this->getTokenAuthenticator();
        $this->assertEquals($expect, $authenticator->getCredentials($request));
    }

    /**
     * @return \Generator
     */
    public function getCredentialsDataProvider(): \Generator
    {
        yield [
            'token123',
            'token123',
        ];
    }

    /**
     * @return void
     */
    public function testGetUser(): void
    {
        $userProvider = Mockery::mock(UserProviderInterface::class);
        $userRepository = Mockery::mock(UserRepository::class);
        $userRepository->expects('findOneBy')->once();

        $this->entityManager->expects('getRepository')->once()->andReturn($userRepository);
        $authenticator = $this->getTokenAuthenticator();

        $this->assertNull($authenticator->getUser(null, $userProvider));
        $this->assertNull($authenticator->getUser([], $userProvider));
    }

    /**
     * @return void
     */
    public function testCheckCredentials(): void
    {
        $authenticator = $this->getTokenAuthenticator();

        $this->assertTrue($authenticator->checkCredentials([], Mockery::mock(User::class)));
    }

    /**
     * @return void
     */
    public function testOnAuthenticationSuccess(): void
    {
        $authenticator = $this->getTokenAuthenticator();

        $this->assertNull($authenticator->onAuthenticationSuccess(
            Mockery::mock(Request::class),
            Mockery::mock(TokenInterface::class),
            ''
        ));
    }

    /**
     * @return void
     */
    public function testOnAuthenticationFailure(): void
    {
        $authenticator = $this->getTokenAuthenticator();

        $this->assertEquals(
            '{"message":"Invalid token."}',
            $authenticator->onAuthenticationFailure(
                Mockery::mock(Request::class),
                Mockery::mock(AuthenticationException::class)
            )->getContent()
        );
    }

    /**
     * @return void
     */
    public function testStart(): void
    {
        $authenticator = $this->getTokenAuthenticator();

        $this->assertEquals(
            '{"message":"Authentication Required."}',
            $authenticator->start(
                Mockery::mock(Request::class),
                Mockery::mock(AuthenticationException::class)
            )->getContent()
        );
    }

    /**
     * @return void
     */
    public function testSupportsRememberMe(): void
    {
        $authenticator = $this->getTokenAuthenticator();

        $this->assertFalse($authenticator->supportsRememberMe());
    }

    /**
     * @return TokenAuthenticator
     */
    private function getTokenAuthenticator(): TokenAuthenticator
    {
        return new TokenAuthenticator(
            $this->entityManager
        );
    }
}
