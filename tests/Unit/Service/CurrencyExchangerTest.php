<?php

namespace App\Service;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CurrencyExchangerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var FilesystemAdapter|MockInterface
     */
    private $cache;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->cache = $this
            ->getMockBuilder(FilesystemAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testInstance(): void
    {
        $this->cache->method('get')->willReturn([
            'BTC' => [
                'base' => 'BTC',
                'price' => 1000,
            ],
        ]);
        $exchanger = $this->getCurrencyExchanger();

        $this->assertEquals(123000.00, $exchanger->convertToUSD('BTC', 123));
    }

    /**
     * @return CurrencyExchanger
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getCurrencyExchanger(): CurrencyExchanger
    {
        return new CurrencyExchanger(
            $this->cache
        );
    }
}
