<?php

namespace App\Service;

use App\Entity\Asset;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CurrencyExchanger
{
    private const API_URL = 'https://api.cryptonator.com/api/ticker/';
    private const CACHE_KEY = 'currencies';

    /**
     * @var FilesystemAdapter
     */
    private FilesystemAdapter $cache;

    /**
     * @var array
     */
    private array $currencyRates;

    /**
     * @param FilesystemAdapter $cache
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function __construct(FilesystemAdapter $cache)
    {
        $this->cache = $cache;
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
        return $this->cache->get(self::CACHE_KEY, function () {
            $currencies = [];
            foreach (Asset::AVAILABLE_CURRENCIES as $availableCurrency) {
                $jsonData = \file_get_contents(self::API_URL . \strtolower($availableCurrency) . '-usd');
                $data = \json_decode($jsonData, true);

                $currencies[$data['ticker']['base']] = [
                    'base' => $data['ticker']['base'],
                    'target' => $data['ticker']['target'],
                    'price' => $data['ticker']['price'],
                ];
            }

            return $currencies;
        });
    }
}
