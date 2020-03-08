<?php

namespace App\Tests\Unit\Wrapper;

use App\Adapter\AssetPricesAdapter;
use App\Entity\Asset;
use App\Model\Asset\Value;
use App\Model\Response\ListResponse;
use App\Repository\AssetRepository;
use App\Wrapper\ListResponseWrapper;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use App\Model\Asset\Asset as AssetModel;
use Symfony\Component\Security\Core\User\UserInterface;

class ListResponseWrapperTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var AssetRepository|MockInterface
     */
    private $assetRepository;

    /**
     * @var AssetPricesAdapter|MockInterface
     */
    private $assetPricesAdapter;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->assetRepository = Mockery::mock(AssetRepository::class);
        $this->assetPricesAdapter = Mockery::mock(AssetPricesAdapter::class);
    }

    /**
     * @param ListResponse $expectedListResponse
     * @param AssetModel   $asset
     *
     * @dataProvider wrapDataProvider
     *
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testWrap(ListResponse $expectedListResponse, AssetModel $asset): void
    {
        $this->assetRepository->expects('findBy')->once()->andReturn([Mockery::mock(Asset::class)]);
        $this->assetPricesAdapter->expects('adapt')->once()->andReturn($asset);

        $listResponseWrapper = $this->getListResponseWrapper();

        $this->assertEquals(
            $expectedListResponse,
            $listResponseWrapper->wrap(Mockery::mock(UserInterface::class), 'USD')
        );
    }

    /**
     * @return \Generator
     */
    public function wrapDataProvider(): \Generator
    {
        yield[
            (new ListResponse())
                ->setTotal((new Value())->setCurrency('USD')->setAmount(1.0))
                ->setAssets([(new AssetModel())->setValue((new Value())->setCurrency('USD')->setAmount(1.0))]),
            (new AssetModel())
                ->setValue(
                    (new Value())
                        ->setAmount(1.0)
                        ->setCurrency('USD')
                ),
        ];
    }

    /**
     * @return ListResponseWrapper
     */
    private function getListResponseWrapper(): ListResponseWrapper
    {
        return new ListResponseWrapper($this->assetRepository, $this->assetPricesAdapter);
    }
}
