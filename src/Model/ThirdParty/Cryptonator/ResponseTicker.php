<?php

namespace App\Model\ThirdParty\Cryptonator;

class ResponseTicker
{
    /**
     * @var string
     */
    private string $base;

    /**
     * @var string
     */
    private string $target;

    /**
     * @var string
     */
    private string $price;

    /**
     * @var string
     */
    private string $volume;

    /**
     * @var string
     */
    private string $change;

    /**
     * @return string
     */
    public function getBase(): string
    {
        return $this->base;
    }

    /**
     * @param string $base
     *
     * @return self
     */
    public function setBase(string $base): self
    {
        $this->base = $base;

        return $this;
    }

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @param string $target
     *
     * @return self
     */
    public function setTarget(string $target): self
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price;
    }

    /**
     * @param string $price
     *
     * @return self
     */
    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return string
     */
    public function getVolume(): string
    {
        return $this->volume;
    }

    /**
     * @param string $volume
     *
     * @return self
     */
    public function setVolume(string $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    /**
     * @return string
     */
    public function getChange(): string
    {
        return $this->change;
    }

    /**
     * @param string $change
     *
     * @return self
     */
    public function setChange(string $change): self
    {
        $this->change = $change;

        return $this;
    }
}
