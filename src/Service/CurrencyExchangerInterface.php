<?php

namespace App\Service;

interface CurrencyExchangerInterface
{
    /**
     * @param string $currency
     * @param float  $value
     *
     * @return float
     */
    public function convertToDefaultCurrency(string $currency, float $value): float;
}
