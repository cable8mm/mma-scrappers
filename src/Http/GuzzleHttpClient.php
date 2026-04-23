<?php

namespace Cable8mm\MmaScrapers\Http;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * A concrete implementation of the HttpClientInterface using Guzzle HTTP client.
 *
 * This class is responsible for making HTTP requests and handling responses for the MMA Scrapers project.
 * It provides a simple interface for performing GET requests and can be easily extended to support other HTTP methods if needed.
 * The GuzzleHttpClient class encapsulates the Guzzle client and manages the response internally, allowing for better separation of concerns and easier maintenance.
 * The constructor initializes the Guzzle client with default settings, including a timeout and a custom User-Agent header to identify the scraper bot when making requests to target websites.
 * Overall, this class serves as a robust and flexible HTTP client for the MMA Scrapers project, leveraging the capabilities of the Guzzle library while adhering to the defined interface for consistency and ease of use across the application.
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
