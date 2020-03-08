<?php

namespace App\Model\Response;

use App\Model\Asset\Asset;

class CreatedResponse
{
    /**
     * @var string
     */
    protected string $message = 'Successfully created.';

    /**
     * @var Asset
     */
    private Asset $asset;

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return self
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return Asset
     */
    public function getAsset(): Asset
    {
        return $this->asset;
    }

    /**
     * @param Asset $asset
     *
     * @return self
     */
    public function setAsset(Asset $asset): self
    {
        $this->asset = $asset;

        return $this;
    }
}
