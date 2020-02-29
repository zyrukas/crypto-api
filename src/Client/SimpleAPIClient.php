<?php

namespace App\Client;

class SimpleAPIClient implements APIClientInterface
{
    /**
     * @param string $url
     *
     * @return string
     */
    public function get(string $url): string
    {
        return \file_get_contents($url);
    }
}
