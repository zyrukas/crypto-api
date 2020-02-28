<?php

namespace App\Tests\Unit\Manager;

use App\Entity\Asset;
use App\Entity\User;
use App\Manager\AssetManager;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AssetManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var ValidatorInterface|MockInterface
     */
    private $validator;

    /**
     * @var EntityManagerInterface|MockInterface
     */
    private $entityManager;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = Mockery::mock(ValidatorInterface::class);
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
    }

    /**
     * @param Asset $expected
     * @param array $data
     *
     * @return void
     *
     * @dataProvider createDataProvider
     */
    public function testCreate(Asset $expected, array $data): void
    {
        $assetManager = $this->getAssetManager();
        $newAsset = $assetManager->create(new User(), $data);
        $newAsset->setUid(null);

        $this->assertEquals($expected, $newAsset);
    }

    /**
     * @return \Generator
     */
    public function createDataProvider(): \Generator
    {
        yield [
            (new Asset())
                ->setLabel('binance')
                ->setCurrency('BTC')
                ->setValue(2.0)
                ->setUser(new User()),
            [
                'label' => 'binance',
                'currency' => 'BTC',
                'value' => 2.0,
            ],
        ];
    }

    /**
     * @return void
     */
    public function testSave(): void
    {
        $this->entityManager->expects('persist')->once();
        $this->entityManager->expects('flush')->once();

        $assetManager = $this->getAssetManager();
        $assetManager->save(new Asset());
    }

    /**
     * @return void
     */
    public function testDelete(): void
    {
        $this->entityManager->expects('remove')->once();
        $this->entityManager->expects('flush')->once();

        $assetManager = $this->getAssetManager();
        $assetManager->delete(new Asset());
    }

    /**
     * @param Asset $oldAsset
     * @param Asset $expected
     * @param array $data
     *
     * @return void
     *
     * @dataProvider updateDataProvider
     */
    public function testUpdate(Asset $oldAsset, Asset $expected, array $data): void
    {
        $assetManager = $this->getAssetManager();
        $newAsset = $assetManager->update($oldAsset, $data);

        $this->assertEquals($expected, $newAsset);
    }

    /**
     * @param Asset $asset
     * @param array $expect
     * @param array $errors
     *
     * @return void
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(Asset $asset, array $expect, array $errors): void
    {
        $this->validator->expects('validate')->once()->andReturn($expect);
        $this->assertEquals($this->getAssetManager()->validate($asset), $errors);
    }

    /**
     * @return \Generator
     */
    public function validateDataProvider(): \Generator
    {
        yield [
            (new Asset())
                ->setLabel('binance')
                ->setCurrency('BTC')
                ->setValue(3.0),
            [],
            [],
        ];
        yield [
            (new Asset())
                ->setLabel('binance')
                ->setCurrency('EUR')
                ->setValue(3.0),
            [new ConstraintViolation('This currency is not available.', null, [], '', 'currency', 'currency')],
            ['currency' => 'This currency is not available.'],
        ];
    }

    /**
     * @return \Generator
     */
    public function updateDataProvider(): \Generator
    {
        yield [
            (new Asset())
                ->setLabel('binance')
                ->setCurrency('BTC')
                ->setValue(3.0),
            (new Asset())
                ->setLabel('binance')
                ->setCurrency('BTC')
                ->setValue(2.0),
            [
                'value' => 2.0,
            ],
        ];
        yield [
            (new Asset())
                ->setLabel('binance')
                ->setCurrency('BTC')
                ->setValue(3.0),
            (new Asset())
                ->setLabel('binance')
                ->setCurrency('BTC')
                ->setValue(3.0),
            [
                'label' => 'binance',
                'currency' => 'BTC',
            ],
        ];
    }

    /**
     * @return AssetManager
     */
    private function getAssetManager(): AssetManager
    {
        return new AssetManager(
            $this->validator,
            $this->entityManager
        );
    }
}
