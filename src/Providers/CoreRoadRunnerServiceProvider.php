<?php

declare(strict_types=1);

namespace Dezer32\Temporal\Laravel\Core\Providers;

use Dezer32\Temporal\Laravel\Core\Console\Commands\ServerInitCommand;
use Dezer32\Temporal\Laravel\Core\Services\WorkerService;
use Dezer32\Temporal\Laravel\Core\Services\WorkerServiceInterface;
use Illuminate\Support\ServiceProvider;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Http\PSR7WorkerInterface;
use Spiral\RoadRunner\Worker;
use Spiral\RoadRunner\WorkerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;

class CoreRoadRunnerServiceProvider extends ServiceProvider
{
    private array $commands = [
        ServerInitCommand::class,
    ];
    public array $bindings = [
        ServerRequestFactoryInterface::class => Psr17Factory::class,
        StreamFactoryInterface::class => Psr17Factory::class,
        UploadedFileFactoryInterface::class => Psr17Factory::class,
        ResponseFactoryInterface::class => Psr17Factory::class,
        PSR7WorkerInterface::class => PSR7Worker::class,
        HttpFoundationFactoryInterface::class => HttpFoundationFactory::class,
        HttpMessageFactoryInterface::class => PsrHttpFactory::class,
        WorkerServiceInterface::class => WorkerService::class,
    ];
    public array $singletons = [
        Psr17Factory::class,
    ];

    public function register(): void
    {
        $this->app->bind(WorkerInterface::class, static fn(): WorkerInterface => Worker::create());

        $this->registerCommands();
    }

    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }
    }
}
