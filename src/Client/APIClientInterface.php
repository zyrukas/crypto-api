<?php

namespace App\Client;

interface APIClientInterface
{
    /**
     * @param string $url
     *
     * @return string
     */
    public function get(string $url): string;
}
