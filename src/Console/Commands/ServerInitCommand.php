<?php

declare(strict_types=1);

namespace Dezer32\Temporal\Laravel\Core\Console\Commands;

use Dezer32\Temporal\Laravel\Core\Services\WorkerServiceInterface;
use Illuminate\Console\Command;

class ServerInitCommand extends Command
{
    protected static $defaultName = 'temporal-project:server:init';
    protected static $defaultDescription = 'Command of init app server';

    public function handle(WorkerServiceInterface $service): void
    {
        $service->handle();
    }
}
