<?php

namespace App\Tests\Unit\Adapter;

use App\Adapter\AssetPricesAdapter;
use App\Entity\Asset;
use App\Model\Asset\Value;
use App\Service\CurrencyExchanger;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use App\Model\Asset\Asset as AssetModel;

class AssetPricesAdapterTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var CurrencyExchanger|MockInterface
     */
    private $currencyExchanger;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->currencyExchanger = Mockery::mock(CurrencyExchanger::class);
    }

    /**
     * @param AssetModel $expect
     * @param Asset      $provide
     *
     * @dataProvider adaptDataProvider
     *
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testAdapt(AssetModel $expect, Asset $provide): void
    {
        $this->currencyExchanger->expects('convertToDefaultCurrency')->once()->andReturn(1000);

        $pricesAdapter = $this->getAssetPricesAdapter();

        $this->assertEquals($expect, $pricesAdapter->adapt($provide));
    }

    /**
     * @return \Generator
     */
    public function adaptDataProvider(): \Generator
    {
        yield [
            (new AssetModel())
                ->setUid('123')
                ->setLabel('binance')
                ->setBaseValue(
                    (new Value())
                        ->setCurrency('BTC')
                        ->setAmount(2.0)
                )
                ->setValue((new Value())
                    ->setCurrency('USD')
                    ->setAmount(1000.0)),
            (new Asset())
                ->setUid('123')
                ->setLabel('binance')
                ->setAmount(2.0)
                ->setCurrency('BTC'),
        ];
    }

    /**
     * @return AssetPricesAdapter
     */
    private function getAssetPricesAdapter(): AssetPricesAdapter
    {
        return new AssetPricesAdapter($this->currencyExchanger, 'USD');
    }
}
