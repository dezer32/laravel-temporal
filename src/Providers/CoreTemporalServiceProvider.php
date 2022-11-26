<?php

declare(strict_types=1);

namespace Dezer32\Temporal\Laravel\Core\Providers;

use Dezer32\Temporal\Laravel\Core\Console\Commands\WorkflowInitCommand;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\GRPC\ServiceClientInterface;
use Temporal\Client\WorkflowClient;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Worker\WorkerFactoryInterface;
use Temporal\Worker\WorkerInterface;
use Temporal\WorkerFactory;

class CoreTemporalServiceProvider extends ServiceProvider
{
    private array $commands = [
        WorkflowInitCommand::class,
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/workflow.php', 'workflow');

        $this->app->singleton(
            WorkerFactoryInterface::class,
            static fn(): WorkerFactoryInterface => WorkerFactory::create()
        );

        $this->app->singleton(WorkerInterface::class, static function (Application $app) {
            return $app->get(WorkerFactoryInterface::class)->newWorker();
        });

        $this->app->bind(
            ServiceClientInterface::class,
            static fn(): ServiceClientInterface => ServiceClient::create((string) Config::get('workflow.address'))
        );

        $this->app->singleton(
            WorkflowClientInterface::class,
            static fn(Application $app) => WorkflowClient::create($app->get(ServiceClientInterface::class))
        );

        $this->registerCommands();
    }

    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }
    }
}
