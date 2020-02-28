<?php

namespace App\Service;

use App\Entity\Asset;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CurrencyExchanger
{
    private const API_URL = 'https://api.cryptonator.com/api/ticker/';
    private const CACHE_KEY = 'currencies';

    /**
     * @var array
     */
    private array $currencyRates;

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function __construct()
    {
        $this->currencyRates = $this->getCurrencyRates();
    }

    /**
     * @param string $currency
     * @param float  $value
     *
     * @return float
     */
    public function convertToUSD(string $currency, float $value): float
    {
        return \round($this->currencyRates[$currency]['price'] * $value, 20);
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getCurrencyRates(): array
    {
        $cachePool = new FilesystemAdapter('', 60);
        $currenciesItem = $cachePool->getItem(self::CACHE_KEY);

        if (!$currenciesItem->isHit()) {
            $currencies = [];
            foreach (Asset::AVAILABLE_CURRENCIES as $availableCurrency) {
                $data = \json_decode($this->getJsonFromApi($availableCurrency), true);

                $currencies[$data['ticker']['base']] = [
                    'base' => $data['ticker']['base'],
                    'target' => $data['ticker']['target'],
                    'price' => $data['ticker']['price'],
                ];
            }

            $currenciesItem->set($currencies);
            $cachePool->save($currenciesItem);
        }

        return $currenciesItem->get();
    }

    /**
     * @param string $availableCurrency
     *
     * @return string
     */
    private function getJsonFromApi(string $availableCurrency): string
    {
        return \file_get_contents(self::API_URL . \strtolower($availableCurrency) . '-usd');
    }
}
