<?php

namespace App\Tests\Unit\Service;

use App\Client\APIClientInterface;
use App\Service\CurrencyExchanger;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class CurrencyExchangerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var APIClientInterface|MockInterface
     */
    private $client;

    /**
     * @var FilesystemAdapter|MockInterface
     */
    private $filesystemAdapter;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = Mockery::mock(APIClientInterface::class);
        $this->filesystemAdapter = Mockery::mock(FilesystemAdapter::class);
    }

    /**
     * @param array $currencies
     * @param array $apiResponse
     * @param float $expect
     * @param array $convert
     *
     * @dataProvider convertToUSDDataProvider
     *
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testConvertToUSD(array $currencies, array $apiResponse, float $expect, array $convert): void
    {
        $itemMock = Mockery::mock(ItemInterface::class);
        $itemMock->expects('expiresAfter')->once();
        $itemMock->expects('isHit')->once();
        $itemMock->expects('set')->once();
        $itemMock->expects('get')->once()->andReturn($currencies);

        $this->client->expects('get')->times(3)->andReturn(\json_encode($apiResponse));

        $this->filesystemAdapter->expects('getItem')->once()->andReturn($itemMock);
        $this->filesystemAdapter->expects('save')->once();

        $this->assertEquals($expect, $this->getCurrencyExchanger()->convertToUSD($convert[0], $convert[1]));
    }

    /**
     * @return \Generator
     */
    public function convertToUSDDataProvider(): \Generator
    {
        yield[
            [
                'BTC' => [
                    'base' => 'BTC',
                    'target' => 'USD',
                    'price' => '8674.55732728',
                ],
                'ETH' => [
                    'base' => 'ETH',
                    'target' => 'USD',
                    'price' => '224.88907104',
                ],
                'IOTA' => [
                    'base' => 'IOTA',
                    'target' => 'USD',
                    'price' => '0.21850000',
                ],
            ],
            [
                'ticker' => [
                    'base' => 'BTC',
                    'target' => 'USD',
                    'price' => '8707.52844257',
                    'volume' => '93710.23117613',
                    'change' => '62.30764273',
                ],
                'timestamp' => 1582977723,
                'success' => true,
                'error' => '',
            ],
            867455.7327279999,
            ['BTC', 100],
        ];
    }

    /**
     * @return CurrencyExchanger
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getCurrencyExchanger(): CurrencyExchanger
    {
        return new CurrencyExchanger($this->client, $this->filesystemAdapter);
    }
}
