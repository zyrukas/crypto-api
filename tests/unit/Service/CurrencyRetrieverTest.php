<?php

namespace App\Tests\Unit\Service;

use App\Client\APIClientInterface;
use App\Model\Cryptonator\Response;
use App\Model\Cryptonator\ResponseTicker;
use App\Service\CurrencyRetriever;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CurrencyRetrieverTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var SerializerInterface|MockInterface
     */
    private $serializer;

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
        $this->serializer = Mockery::mock(SerializerInterface::class);
        $this->client = Mockery::mock(APIClientInterface::class);
        $this->filesystemAdapter = Mockery::mock(FilesystemAdapter::class);
    }

    /**
     * @param array    $currencies
     * @param array    $apiResponse
     * @param Response $deserialized
     * @param array    $expect
     *
     * @dataProvider getCurrenciesDataProvider
     *
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testGetCurrencies(
        array $currencies,
        array $apiResponse,
        Response $deserialized,
        array $expect
    ): void {
        $itemMock = Mockery::mock(ItemInterface::class);
        $itemMock->expects('expiresAfter')->once();
        $itemMock->expects('isHit')->once();
        $itemMock->expects('set')->once();
        $itemMock->expects('get')->once()->andReturn($currencies);

        $this->client->expects('get')->times(3)->andReturn(\json_encode($apiResponse));

        $this->filesystemAdapter->expects('getItem')->once()->andReturn($itemMock);
        $this->filesystemAdapter->expects('save')->once();

        $this->serializer->expects('deserialize')->times(3)->andReturn($deserialized);

        $currencyRetriever = $this->getCurrencyRetriever();

        $this->assertEquals($expect, $currencyRetriever->getCurrencies());
    }

    /**
     * @return \Generator
     */
    public function getCurrenciesDataProvider(): \Generator
    {
        yield[
            [
                'BTC' => [
                    'base' => 'BTC',
                    'target' => 'USD',
                    'price' => '8674.55732728',
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
            (new Response())
                ->setError('')
                ->setSuccess(true)
                ->setTimestamp(1583068742)
                ->setTicker(
                    (new ResponseTicker())
                        ->setPrice('8707.52844257')
                        ->setTarget('USD')
                        ->setBase('BTC')
                        ->setChange('62.30764273')
                        ->setVolume('93710.23117613')
                ),
            [
                'BTC' => [
                    'base' => 'BTC',
                    'target' => 'USD',
                    'price' => '8674.55732728',
                ],
            ],
        ];
    }

    /**
     * @return CurrencyRetriever
     */
    private function getCurrencyRetriever(): CurrencyRetriever
    {
        return new CurrencyRetriever($this->serializer, $this->client, 'USD', $this->filesystemAdapter);
    }
}
