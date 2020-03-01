<?php

namespace App\Tests\Unit\Service;

use App\Model\Cryptonator\Response;
use App\Model\Cryptonator\ResponseTicker;
use App\Service\CurrencyExchanger;
use App\Service\CurrencyRetriever;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class CurrencyExchangerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var CurrencyRetriever|MockInterface
     */
    private $currencyRetriever;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->currencyRetriever = Mockery::mock(CurrencyRetriever::class);
    }

    /**
     * @param array $currencies
     * @param float $expect
     * @param array $convert
     *
     * @dataProvider convertToUSDDataProvider
     *
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testConvertToDefaultCurrency(
        array $currencies,
        float $expect,
        array $convert
    ): void {
        $this->currencyRetriever->expects('getCurrencies')->once()->andReturn($currencies);

        $this->assertEquals($expect, $this->getCurrencyExchanger()->convertToDefaultCurrency($convert[0], $convert[1]));
    }

    /**
     * @return \Generator
     */
    public function convertToUSDDataProvider(): \Generator
    {
        yield[
            [
                'BTC' => (new Response())
                    ->setTimestamp(1583068084)
                    ->setError('')
                    ->setSuccess(true)
                    ->setTicker(
                        (new ResponseTicker())
                            ->setBase('BTC')
                            ->setTarget('USD')
                            ->setPrice('8674.55732728')
                    ),
            ],
            867455.7327279999,
            ['BTC', 100],
        ];
    }

    /**
     * @return CurrencyExchanger
     */
    private function getCurrencyExchanger(): CurrencyExchanger
    {
        return new CurrencyExchanger($this->currencyRetriever);
    }
}
