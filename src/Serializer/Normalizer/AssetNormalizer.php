<?php

namespace App\Serializer\Normalizer;

use App\Entity\Asset;
use App\Service\CurrencyExchangerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AssetNormalizer implements ContextAwareNormalizerInterface
{
    /**
     * @var ObjectNormalizer
     */
    private ObjectNormalizer $normalizer;

    /**
     * @var CurrencyExchangerInterface
     */
    private CurrencyExchangerInterface $currencyExchanger;

    /**
     * @param ObjectNormalizer           $normalizer
     * @param CurrencyExchangerInterface $currencyExchanger
     */
    public function __construct(ObjectNormalizer $normalizer, CurrencyExchangerInterface $currencyExchanger)
    {
        $this->normalizer = $normalizer;
        $this->currencyExchanger = $currencyExchanger;
    }

    /**
     * @param Asset       $asset
     * @param string|null $format
     * @param array       $context
     *
     * @return array
     */
    public function normalize($asset, string $format = null, array $context = []): array
    {
        return [
            'uid' => $asset->getUid(),
            'label' => $asset->getLabel(),
            'currency' => $asset->getCurrency(),
            'value' => $asset->getValue(),
            'valueInUSD' => $this->currencyExchanger->convertToUSD($asset->getCurrency(), $asset->getValue()),
        ];
    }

    /**
     * @param mixed       $data
     * @param string|null $format
     * @param array       $context
     *
     * @return bool
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Asset;
    }
}
