<?php

namespace App\Model\Cryptonator;

class Response
{
    /**
     * @var ResponseTicker
     */
    private ResponseTicker $ticker;

    /**
     * @var int
     */
    private int $timestamp;

    /**
     * @var bool
     */
    private bool $success;

    /**
     * @var string
     */
    private string $error;

    /**
     * @return ResponseTicker
     */
    public function getTicker(): ResponseTicker
    {
        return $this->ticker;
    }

    /**
     * @param ResponseTicker $ticker
     *
     * @return self
     */
    public function setTicker(ResponseTicker $ticker): self
    {
        $this->ticker = $ticker;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     *
     * @return self
     */
    public function setTimestamp(int $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @param bool $success
     *
     * @return self
     */
    public function setSuccess(bool $success): self
    {
        $this->success = $success;

        return $this;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @param string $error
     *
     * @return self
     */
    public function setError(string $error): self
    {
        $this->error = $error;

        return $this;
    }
}
