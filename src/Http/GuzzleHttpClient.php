<?php

namespace Cable8mm\MmaScrapers\Http;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * Class GuzzleHttpClient
 *
 * A concrete implementation of the HttpClientInterface using Guzzle HTTP client.
 */
class GuzzleHttpClient implements HttpClientInterface
{
    /* @var Client The Guzzle HTTP client instance. */
    private Client $client;

    /* @var ResponseInterface The most recent HTTP response received. */
    private ResponseInterface $response;

    /**
     * GuzzleHttpClient constructor.
     *
     * Initializes the Guzzle HTTP client with default settings, including a timeout and a custom User-Agent header.
     */
    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10,
            'headers' => [
                'User-Agent' => 'MMA Scraper Bot',
            ],
        ]);
    }

    /**
     * Perform an HTTP GET request to the specified URL and return the response body as a string.
     *
     * @param string $url The URL to send the GET request to.
     * @return string The response body from the GET request.
     */
    public function get(string $url): string
    {
        $this->response = $this->client->get($url);

        return (string) $this->response->getBody();
    }
}
