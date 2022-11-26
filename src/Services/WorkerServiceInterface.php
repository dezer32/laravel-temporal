<?php

namespace Dezer32\Temporal\Laravel\Core\Services;

interface WorkerServiceInterface
{
    public function handle(): void;
}
