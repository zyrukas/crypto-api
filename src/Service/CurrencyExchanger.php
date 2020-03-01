<?php

namespace App\Service;

class CurrencyExchanger implements CurrencyExchangerInterface
{
    /**
     * @var CurrencyRetriever
     */
    private CurrencyRetriever $currencyRetriever;

    /**
     * @param CurrencyRetriever $currencyRetriever
     */
    public function __construct(CurrencyRetriever $currencyRetriever)
    {
        $this->currencyRetriever = $currencyRetriever;
    }

    /**
     * @param string $currency
     * @param float  $value
     *
     * @return float
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function convertToDefaultCurrency(string $currency, float $value): float
    {
        return \round($this->currencyRetriever->getCurrencies()[$currency]->getTicker()->getPrice() * $value, 20);
    }
}
