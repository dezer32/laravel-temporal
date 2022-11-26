<?php

declare(strict_types=1);

namespace Dezer32\Temporal\Laravel\Core\Exceptions;

class InheritLaravelTemporalException extends LaravelTemporalException
{
    private const MESSAGE = "Class %s does not inherit interface %s.";

    public function __construct(string $interface, string $workflow)
    {
        $message = sprintf(self::MESSAGE, $workflow, $interface);

        parent::__construct($message);
    }
}
