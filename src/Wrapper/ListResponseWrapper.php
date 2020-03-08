<?php

namespace App\Wrapper;

use App\Adapter\AssetPricesAdapter;
use App\Model\Asset\Value;
use App\Model\Response\ListResponse;
use App\Repository\AssetRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class ListResponseWrapper
{
    /**
     * @var AssetRepository
     */
    private AssetRepository $assetRepository;

    /**
     * @var AssetPricesAdapter
     */
    private AssetPricesAdapter $assetPricesAdapter;

    /**
     * @param AssetRepository    $assetRepository
     * @param AssetPricesAdapter $assetPricesAdapter
     */
    public function __construct(AssetRepository $assetRepository, AssetPricesAdapter $assetPricesAdapter)
    {
        $this->assetRepository = $assetRepository;
        $this->assetPricesAdapter = $assetPricesAdapter;
    }

    /**
     * @param UserInterface $user
     * @param string        $defaultCurrency
     *
     * @return ListResponse
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function wrap(UserInterface $user, string $defaultCurrency): ListResponse
    {
        $total = 0;
        $response = new ListResponse();
        foreach ($this->assetRepository->findBy(['user' => $user]) as $asset) {
            $adaptedAsset = $this->assetPricesAdapter->adapt($asset);
            $response->addAssets($adaptedAsset);
            $total += $adaptedAsset->getValue()->getAmount();
        }

        return $response->setTotal(
            (new Value())
                ->setAmount($total)
                ->setCurrency($defaultCurrency)
        );
    }
}
