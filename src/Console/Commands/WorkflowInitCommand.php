<?php

declare(strict_types=1);

namespace Dezer32\Temporal\Laravel\Core\Console\Commands;

use Illuminate\Console\Command;
use Temporal\Worker\WorkerFactoryInterface;

class WorkflowInitCommand extends Command
{
    protected static $defaultName = 'temporal-project:workflow:init';
    protected static $defaultDescription = 'Command of init workflows';

    public function handle(WorkerFactoryInterface $factory): void
    {
        $factory->run();
    }
}
