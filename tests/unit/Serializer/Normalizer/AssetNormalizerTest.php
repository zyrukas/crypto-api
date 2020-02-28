<?php

namespace App\Tests\Unit\Serializer\Normalizer;

use App\Entity\Asset;
use App\Entity\User;
use App\Serializer\Normalizer\AssetNormalizer;
use App\Service\CurrencyExchanger;
use Mockery;
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
        $this->normalizer = Mockery::mock(ObjectNormalizer::class);
        $this->currencyExchanger = Mockery::mock(CurrencyExchanger::class);
    }

    /**
     * @param Asset $asset
     * @param array $expect
     *
     * @return void
     *
     * @dataProvider normalizeDataProvider
     */
    public function testNormalize(Asset $asset, array $expect): void
    {
        $this->currencyExchanger->expects('convertToUSD')->once();

        $assetNormalizer = $this->getAssetNormalizer();

        $this->assertTrue($assetNormalizer->supportsNormalization($asset));
        $this->assertEquals($expect, $assetNormalizer->normalize($asset));
    }

    /**
     * @return \Generator
     */
    public function normalizeDataProvider(): \Generator
    {
        yield [
            (new Asset())
                ->setLabel('binance')
                ->setCurrency('BTC')
                ->setValue(2.0)
                ->setUser(Mockery::mock(User::class)),
            [
                'uid' => null,
                'label' => 'binance',
                'currency' => 'BTC',
                'value' => 2.0,
                'valueInUSD' => 0.0,
            ],
        ];
    }

    /**
     * @return AssetNormalizer
     */
    private function getAssetNormalizer(): AssetNormalizer
    {
        return new AssetNormalizer(
            $this->normalizer,
            $this->currencyExchanger
        );
    }
}
