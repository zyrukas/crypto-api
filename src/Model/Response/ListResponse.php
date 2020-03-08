<?php

namespace App\Model\Response;

use App\Model\Asset\Asset;
use App\Model\Asset\Value;

class ListResponse
{
    /**
     * @var Value
     */
    private Value $total;

    /**
     * @var Asset[]
     */
    private array $assets;

    /**
     * @return Value
     */
    public function getTotal(): Value
    {
        return $this->total;
    }

    /**
     * @param Value $total
     *
     * @return self
     */
    public function setTotal(Value $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @param Asset $asset
     *
     * @return $this
     */
    public function addAssets(Asset $asset): self
    {
        $this->assets[] = $asset;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasAssets(): bool
    {
        return $this->assets !== null;
    }

    /**
     * @return Asset[]
     */
    public function getAssets(): array
    {
        return $this->assets;
    }

    /**
     * @param Asset[] $assets
     *
     * @return self
     */
    public function setAssets(array $assets): self
    {
        $this->assets = $assets;

        return $this;
    }
}
