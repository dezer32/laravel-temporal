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
        $this->publishes([
            __DIR__ . '/../../config/.rr.yaml' => $this->app->basePath('.rr.yaml'),
            __DIR__ . '/../../config/Dockerfile' => $this->app->basePath('Dockerfile'),
            __DIR__ . '/../../config/docker-compose.yml' => $this->app->basePath('docker-compose.yml'),
        ], 'laravel-temporal-docker');
    }

    private function registerProviders(): void
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }
}
