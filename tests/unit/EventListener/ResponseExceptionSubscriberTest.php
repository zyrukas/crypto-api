<?php

namespace App\Tests\Unit\EventListener;

use App\EventListener\ResponseExceptionSubscriber;
use App\Exception\JsonResponseException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ResponseExceptionSubscriberTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @param HttpExceptionInterface $exception
     *
     * @return void
     *
     * @dataProvider onKernelExceptionDataProvider
     */
    public function testOnKernelException(HttpExceptionInterface $exception): void
    {
        $responseSubscriber = $this->getResponseExceptionSubscriber();

        $this->assertInstanceOf(ResponseExceptionSubscriber::class, $responseSubscriber);

        $appEnvTemp = $_ENV['APP_ENV'];
        $_ENV['APP_ENV'] = 'dev';
        $responseSubscriber->onKernelException(new ExceptionEvent( // final class, can't mock.
            Mockery::mock(HttpKernelInterface::class),
            Mockery::mock(Request::class),
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        ));
        $_ENV['APP_ENV'] = $appEnvTemp;

        $responseSubscriber->onKernelException(new ExceptionEvent( // final class, can't mock.
            Mockery::mock(HttpKernelInterface::class),
            Mockery::mock(Request::class),
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        ));
    }

    /**
     * @return \Generator
     */
    public function onKernelExceptionDataProvider(): \Generator
    {
        $jsonException = Mockery::mock(JsonResponseException::class);
        $jsonException->expects('getMessages')->once();
        $jsonException->expects('getStatusCode')->once()->andReturn(Response::HTTP_BAD_REQUEST);

        yield [$jsonException];
        yield [Mockery::mock(HttpException::class)];
    }

    /**
     * @return void
     */
    public function testGetSubscribedEvents(): void
    {
        $responseSubscriber = $this->getResponseExceptionSubscriber();

        $this->assertEquals(['kernel.exception' => 'onKernelException'], $responseSubscriber::getSubscribedEvents());
    }

    /**
     * @return ResponseExceptionSubscriber
     */
    private function getResponseExceptionSubscriber(): ResponseExceptionSubscriber
    {
        return new ResponseExceptionSubscriber();
    }
}
