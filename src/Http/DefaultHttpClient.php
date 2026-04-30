<?php

namespace Cable8mm\MmaScrapers\Http;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use GuzzleHttp\Client;

/**
 * A default implementation of the HttpClientInterface using Guzzle HTTP client.
 *
 * This class provides a simple way to perform HTTP GET requests and can be easily extended to support other HTTP methods if needed.
 * The constructor initializes the Guzzle client with default settings, including a timeout and a custom User-Agent header to identify the scraper bot when making requests to target websites.
 * Overall, this class serves as a basic HTTP client for the MMA Scrapers project, leveraging the capabilities of the Guzzle library while adhering to the defined interface for consistency and ease of use across the application.
 */
class DefaultHttpClient implements HttpClientInterface
{
    /**
     * Initializes the DefaultHttpClient with a Guzzle HTTP client.
     */
    public function __construct(
        private Client $client = new Client([
            'timeout' => 10,
            'headers' => [
                'User-Agent' => 'mma-scrapers'
            ]
        ])
    ) {
    }

    /**
     * Perform an HTTP GET request to the specified URL and return the response body as a string.
     *
     * @param string $url The URL to send the GET request to.
     * @return string The response body from the GET request.
     */
    public function get(string $url): string
    {
        return (string) $this->client->get($url)->getBody();
    }
}
