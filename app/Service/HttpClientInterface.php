<?php

namespace App\Service;

/**
 * Interface HttpClientInterface
 * @package App\Service
 */
interface HttpClientInterface
{
    /**
     * @param string $url
     * @param array $headers
     * @return string
     */
    public function getPageHtml(string $url, array $headers): string;
}
