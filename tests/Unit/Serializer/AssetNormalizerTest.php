<?php

namespace App\Tests\Unit\Manager;

use App\Entity\Asset;
use App\Serializer\AssetNormalizer;
use App\Service\CurrencyExchanger;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AssetNormalizerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var ObjectNormalizer|MockInterface
     */
    private $normalizer;

    /**
     * @var CurrencyExchanger|MockInterface
     */
    private $currencyExchanger;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->normalizer = $this
            ->getMockBuilder(ObjectNormalizer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->currencyExchanger = $this
            ->getMockBuilder(CurrencyExchanger::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return void
     */
    public function testNormalizeAsset(): void
    {
        $normalizer = $this->getAssetNormalizer();

        $normalized = $normalizer->normalizeAsset(
            (new Asset())
                ->setLabel('binance')
                ->setCurrency('BTC')
                ->setValue(10)
        );

        $this->assertEquals([
            'uid' => null,
            'label' => 'binance',
            'currency' => 'BTC',
            'value' => 10.00,
            'valueInUSD' => 0.0,
        ], $normalized);
    }

    /**
     * @return AssetNormalizer
     */
    public function getAssetNormalizer(): AssetNormalizer
    {
        return new AssetNormalizer(
            $this->normalizer,
            $this->currencyExchanger
        );
    }
}
