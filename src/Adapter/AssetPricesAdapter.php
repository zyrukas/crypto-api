<?php

namespace App\Adapter;

use App\Entity\Asset;
use App\Service\CurrencyExchanger;

class AssetPricesAdapter
{
    /**
     * @var CurrencyExchanger
     */
    private CurrencyExchanger $currencyExchanger;

    /**
     * @param CurrencyExchanger $currencyExchanger
     */
    public function __construct(CurrencyExchanger $currencyExchanger)
    {
        $this->currencyExchanger = $currencyExchanger;
    }

    /**
     * @param Asset $asset
     *
     * @return Asset
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function adapt(Asset $asset): Asset
    {
        return $asset->setValueInDefaultCurrency(
            $this->currencyExchanger->convertToDefaultCurrency($asset->getCurrency(), $asset->getValue())
        );
    }
}
