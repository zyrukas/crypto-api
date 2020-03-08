<?php

namespace App\Model\Asset;

class Asset
{
    /**
     * @var string
     */
    private string $uid;

    /**
     * @var string
     */
    private string $label;

    /**
     * @var Value
     */
    private Value $baseValue;

    /**
     * @var Value
     */
    private Value $value;

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     *
     * @return self
     */
    public function setUid(string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return self
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Value
     */
    public function getBaseValue(): Value
    {
        return $this->baseValue;
    }

    /**
     * @param Value $baseValue
     *
     * @return self
     */
    public function setBaseValue(Value $baseValue): self
    {
        $this->baseValue = $baseValue;

        return $this;
    }

    /**
     * @return Value
     */
    public function getValue(): Value
    {
        return $this->value;
    }

    /**
     * @param Value $value
     *
     * @return self
     */
    public function setValue(Value $value): self
    {
        $this->value = $value;

        return $this;
    }
}
