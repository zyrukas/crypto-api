<?php

namespace App\Adapter;

use App\Entity\Asset;
use App\Model\Asset\Asset as AssetModel;
use App\Model\Asset\Value;
use App\Service\CurrencyExchanger;

class AssetPricesAdapter
{
    /**
     * @var CurrencyExchanger
     */
    private CurrencyExchanger $currencyExchanger;

    /**
     * @var string
     */
    private string $defaultCurrency;

    /**
     * @param CurrencyExchanger $currencyExchanger
     * @param string            $defaultCurrency
     */
    public function __construct(CurrencyExchanger $currencyExchanger, string $defaultCurrency)
    {
        $this->currencyExchanger = $currencyExchanger;
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * @param Asset $asset
     *
     * @return AssetModel
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function adapt(Asset $asset): AssetModel
    {
        return (new AssetModel())
            ->setUid($asset->getUid())
            ->setLabel($asset->getLabel())
            ->setBaseValue(
                (new Value())
                    ->setCurrency($asset->getCurrency())
                    ->setAmount($asset->getAmount())
            )
            ->setValue(
                (new Value())
                    ->setCurrency($this->defaultCurrency)
                    ->setAmount(
                        $this->currencyExchanger->convertToDefaultCurrency($asset->getCurrency(), $asset->getAmount())
                    )
            );
    }
}
