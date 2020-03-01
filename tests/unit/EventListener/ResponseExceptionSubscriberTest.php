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
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ResponseExceptionSubscriberTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @return void
     */
    public function testOnKernelException(): void
    {
        $responseSubscriber = $this->getResponseExceptionSubscriber();

        // Can't use Generator because of kernelException case.
        $jsonException = Mockery::mock(JsonResponseException::class);
        $jsonException->expects('getMessages')->once();
        $jsonException->expects('getStatusCode')->once()->andReturn(Response::HTTP_BAD_REQUEST);

        $appEnvTemp = $_ENV['APP_ENV'];
        $_ENV['APP_ENV'] = 'dev';
        $responseSubscriber->onKernelException(new ExceptionEvent( // final class, can't mock.
            Mockery::mock(HttpKernelInterface::class),
            Mockery::mock(Request::class),
            HttpKernelInterface::MASTER_REQUEST,
            $jsonException
        ));
        $_ENV['APP_ENV'] = $appEnvTemp;

        $responseSubscriber->onKernelException(new ExceptionEvent( // final class, can't mock.
            Mockery::mock(HttpKernelInterface::class),
            Mockery::mock(Request::class),
            HttpKernelInterface::MASTER_REQUEST,
            $jsonException
        ));
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
