<?php

namespace Cable8mm\MmaScrapers\Laravel;

use Illuminate\Support\ServiceProvider;
use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\Http\DefaultHttpClient;

/**
 * MmaScrapersServiceProvider is a Laravel service provider that registers the HTTP client implementation for the MMA Scrapers project.
 *
 * This service provider binds the HttpClientInterface to a concrete implementation (DefaultHttpClient) in the Laravel service container.
 * It checks if there is already a binding for the HttpClientInterface before registering the default implementation, allowing for flexibility and customization by users of the package.
 * The boot method can be used to perform any additional setup or configuration needed when the service provider is loaded, such as publishing configuration files or setting up event listeners.
 * Overall, this class serves as the entry point for integrating the MMA Scrapers package with a Laravel application, ensuring that the necessary dependencies are properly registered and available for use throughout the application.
 */
class MmaScrapersServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        // Check if the HttpClientInterface is already bound in the service container
        if (! $this->app->bound(HttpClientInterface::class)) {
            $this->app->bind(
                HttpClientInterface::class,
                DefaultHttpClient::class
            );
        }
    }

    public function boot(): void
    {
        // Additional boot logic can be added here if needed, such as publishing configuration files or setting up event listeners.
    }
}
