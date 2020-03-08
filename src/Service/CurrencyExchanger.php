<?php

namespace App\Service;

class CurrencyExchanger implements CurrencyExchangerInterface
{
    private const CURRENCY_PRECISION = 20;

    /**
     * @var CurrencyRetriever
     */
    private CurrencyRetriever $currencyRetriever;

    /**
     * @var array|null
     */
    private ?array $currencies = null;

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
        return \round($this->getCurrencies()[$currency]->getTicker()->getPrice() * $value, self::CURRENCY_PRECISION);
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getCurrencies(): array
    {
        if ($this->currencies === null) {
            $this->currencies = $this->currencyRetriever->getCurrencies();
        }

        return $this->currencies;
    }
}
