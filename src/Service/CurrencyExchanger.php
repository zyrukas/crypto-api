<?php

namespace App\Service;

use App\Client\APIClientInterface;
use App\Entity\Asset;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;

class CurrencyExchanger implements CurrencyExchangerInterface
{
    private const API_URL = 'https://api.cryptonator.com/api/ticker/';
    private const CACHE_KEY = 'currencies';
    private const EXPIRE_AFTER_SECONDS = 60;

    /**
     * @var APIClientInterface
     */
    private APIClientInterface $client;

    /**
     * @var FilesystemAdapter
     */
    private FilesystemAdapter $filesystemAdapter;

    /**
     * @var array
     */
    private array $currencyRates;

    /**
     * @param APIClientInterface $client
     * @param FilesystemAdapter  $filesystemAdapter
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function __construct(APIClientInterface $client, FilesystemAdapter $filesystemAdapter)
    {
        $this->client = $client;
        $this->filesystemAdapter = $filesystemAdapter;
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
        /** @var CacheItem $currenciesItem */
        $currenciesItem = $this->filesystemAdapter->getItem(self::CACHE_KEY);
        $currenciesItem->expiresAfter(self::EXPIRE_AFTER_SECONDS);

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
            $this->filesystemAdapter->save($currenciesItem);
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
        return $this->client->get(self::API_URL . \strtolower($availableCurrency) . '-usd');
    }
}
