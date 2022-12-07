<?php

declare(strict_types=1);

namespace Dezer32\Temporal\Laravel\Core\Providers;

use Dezer32\Temporal\Laravel\Core\Exceptions\InheritLaravelTemporalException;
use Illuminate\Support\ServiceProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Temporal\Worker\WorkerInterface;

abstract class TemporalServiceProvider extends ServiceProvider
{
    protected array $workflowBindings;
    protected array $activityBindings;

    public function register(): void
    {
        $this->bindingsActivity();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InheritLaravelTemporalException
     */
    public function boot(): void
    {
        /** @var WorkerInterface $worker */
        $worker = $this->app->get(WorkerInterface::class);

        $this->registerWorkflows($worker);
        $this->registerActivities($worker);
    }

    private function bindingsActivity(): void
    {
        if (isset($this->activityBindings)) {
            foreach ($this->activityBindings as $interface => $activity) {
                $this->app->bind($interface, $activity);
            }
        }
    }

    /**
     * @throws InheritLaravelTemporalException
     */
    private function registerWorkflows(WorkerInterface $worker): void
    {
        if (isset($this->workflowBindings)) {
            foreach ($this->workflowBindings as $interface => $workflow) {
                if (!is_subclass_of($workflow, $interface)) {
                    throw new InheritLaravelTemporalException($interface, $workflow);
                }

                $worker->registerWorkflowTypes($workflow);
            }
        }
    }

    /**
     * @throws InheritLaravelTemporalException
     */
    private function registerActivities(WorkerInterface $worker): void
    {
        if (isset($this->activityBindings)) {
            foreach ($this->activityBindings as $interface => $activity) {
                if (!is_subclass_of($activity, $interface)) {
                    throw new InheritLaravelTemporalException($interface, $activity);
                }
                $worker->registerActivity($activity, fn() => $this->app->get($interface));
            }
        }
    }
}
