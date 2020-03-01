<?php

namespace App\Serializer\Normalizer;

use App\Entity\Asset;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AssetNormalizer implements ContextAwareNormalizerInterface
{
    /**
     * @var ObjectNormalizer
     */
    private ObjectNormalizer $normalizer;

    /**
     * @param ObjectNormalizer $normalizer
     */
    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
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
            'defaultCurrency' => $asset->getValueInDefaultCurrency(),
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
