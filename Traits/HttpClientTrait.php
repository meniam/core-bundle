<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

trait HttpClientTrait
{
    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var array
     */
    protected $httpRequestHeader = [
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language' => 'ru,en-US;q=0.8,en;q=0.6,en-GB;q=0.4',
        'Cache-Control' => 'max-age=0',
        'Connection' => 'close',
        'Upgrade-Insecure-Requests' => '1',
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36'
    ];

    public function getHttpClient($headers = [], $options = [])
    {
        $defaultOptions = [
            'timeout' => 10.0,
            'verify' => false,
            'headers' => array_merge($this->httpRequestHeader, $headers)
        ];

        if (!$this->httpClient) {
            $this->httpClient = new Client(array_merge($defaultOptions, $options));
        }

        return $this->httpClient;
    }

    public function getUrlContent($url, $options = [])
    {
        $content = null;
        try {
            $response = $this->getHttpClient()->request('GET', $url, $options);
            if ($response->getStatusCode() == 200) {
                $content = $response->getBody();
            }
        } catch (GuzzleException $e) {
            if (!isset($options['max_tries']) || !$options['max_tries']) return $content;
            if (!isset($options['try'])) {
                $options['try'] = 1;
            } else {
                $options['try']++;
            }

            if ($options['try'] <= $options['max_tries']) {
                return $this->getUrlContent($url, $options);
            } else {
                return $content;
            }
        }

        return $content;
    }
}
