<?php

namespace App\Service;

use App\Client\APIClientInterface;
use App\Entity\Asset;
use App\Model\ThirdParty\Cryptonator\Response;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Serializer\SerializerInterface;

class CurrencyRetriever
{
    private const API_URL = 'https://api.cryptonator.com/api/ticker/';
    private const CACHE_KEY = 'currencies_';
    private const EXPIRE_AFTER_SECONDS = 60;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var APIClientInterface
     */
    private APIClientInterface $client;

    /**
     * @var string
     */
    private string $defaultCurrency;

    /**
     * @var FilesystemAdapter
     */
    private FilesystemAdapter $filesystemAdapter;

    /**
     * @param SerializerInterface $serializer
     * @param APIClientInterface  $client
     * @param string              $defaultCurrency
     * @param FilesystemAdapter   $filesystemAdapter
     */
    public function __construct(
        SerializerInterface $serializer,
        APIClientInterface $client,
        string $defaultCurrency,
        FilesystemAdapter $filesystemAdapter
    ) {
        $this->serializer = $serializer;
        $this->client = $client;
        $this->defaultCurrency = $defaultCurrency;
        $this->filesystemAdapter = $filesystemAdapter;
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getCurrencies(): array
    {
        /** @var CacheItem $currenciesItem */
        $currenciesItem = $this->filesystemAdapter->getItem(self::CACHE_KEY . $this->defaultCurrency);
        $currenciesItem->expiresAfter(self::EXPIRE_AFTER_SECONDS);

        if (!$currenciesItem->isHit()) {
            $currencies = [];
            foreach (Asset::AVAILABLE_CURRENCIES as $availableCurrency) {
                $response = $this->getCurrencyRates($availableCurrency);

                $currencies[$response->getTicker()->getBase()] = $response;
            }

            $currenciesItem->set($currencies);
            $this->filesystemAdapter->save($currenciesItem);
        }

        return $currenciesItem->get();
    }

    /**
     * @param string $currency
     *
     * @return Response|object
     */
    private function getCurrencyRates(string $currency): Response
    {
        return $this->serializer->deserialize(
            $this->getJsonFromApi($currency),
            Response::class,
            'json'
        );
    }

    /**
     * @param string $availableCurrency
     *
     * @return string
     */
    private function getJsonFromApi(string $availableCurrency): string
    {
        return $this->client->get(
            self::API_URL . \strtolower($availableCurrency) . '-' . \strtolower($this->defaultCurrency)
        );
    }
}
