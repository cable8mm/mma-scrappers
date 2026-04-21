<?php

namespace Cable8mm\MmaScrapers\Contracts;

/**
 * Interface HttpClientInterface
 *
 * A contract for HTTP clients that can perform GET requests to fetch data from the web.
 */
interface HttpClientInterface
{
    /**
     * Perform an HTTP GET request to the specified URL and return the response body as a string.
     *
     * @param string $url The URL to send the GET request to.
     * @return string The response body from the GET request.
     */
    public function get(string $url): string;
}
