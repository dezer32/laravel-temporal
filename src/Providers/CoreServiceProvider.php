<?php

declare(strict_types=1);

namespace Dezer32\Temporal\Laravel\Core\Providers;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    private array $providers = [
        CoreRoadRunnerServiceProvider::class,
        CoreTemporalServiceProvider::class,
    ];

    public function register(): void
    {
        $this->registerProviders();
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/.rr.yaml' => $this->app->basePath(),
        ], 'laravel-temporal-rr.yaml');

        $this->publishes([
            __DIR__ . '/../../docker/php/8.1-cli/Dockerfile' => $this->app->basePath('docker/php/8.1-cli/Dockerfile'),
            __DIR__ . '/../../docker-compose.yml' => $this->app->basePath(),
            __DIR__ . '/../../.env.temporal' => $this->app->basePath(),
        ], 'laravel-temporal-docker');
    }

    private function registerProviders(): void
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }
}
