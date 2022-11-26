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

    private function registerProviders(): void
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }
}
