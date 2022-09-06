<?php

namespace App\Service;

use App\Constant\Number as NumberConstant;
use App\Exceptions\BadHttpResponseException;
use GuzzleHttp\Client;

/**
 * Class RemoteDataReceiver
 * @package App\Service
 */
class RemoteDataReceiver implements HttpClientInterface
{
    /**
     * @param string $url
     * @param array $headers
     * @return string
     * @throws BadHttpResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPageHtml(string $url, array $headers): string
    {
        $client = new Client();
        $options = [
            'defaults' => [
                'verify' => false
            ]
        ];
        $response = $client->request('GET', $url, array_merge($options, $headers));

        if ($response->getStatusCode() === NumberConstant::HTTP_RESPONSE_CODE_200) {
            return $response->getBody()->getContents();
        }

        throw new BadHttpResponseException(
            sprintf('Server %s has answered with %s code', $url, $response->getStatusCode())
        );
    }
}
