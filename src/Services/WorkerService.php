<?php

declare(strict_types=1);

namespace Dezer32\Temporal\Laravel\Core\Services;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\RoadRunner\Http\PSR7WorkerInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;

class WorkerService implements WorkerServiceInterface
{
    private PSR7WorkerInterface $psr7Worker;
    private Kernel $kernel;
    private HttpFoundationFactoryInterface $symfonyHttpFactory;
    private HttpMessageFactoryInterface $psrHttpFactory;

    // @todo добавить логирование этапов запроса.

    public function __construct(
        PSR7WorkerInterface $psr7Worker,
        Kernel $kernel,
        HttpFoundationFactoryInterface $symfonyHttpFactory,
        HttpMessageFactoryInterface $psrHttpFactory
    ) {
        $this->psr7Worker = $psr7Worker;
        $this->kernel = $kernel;
        $this->symfonyHttpFactory = $symfonyHttpFactory;
        $this->psrHttpFactory = $psrHttpFactory;
    }

    public function handle(): void
    {
        while ($psr7Request = $this->psr7Worker->waitRequest()) {
            if (($psr7Request instanceof ServerRequestInterface) === false) {
                break;
            }

            try {
                $symfonyRequest = $this->symfonyHttpFactory->createRequest($psr7Request);

                $laravelRequest = Request::createFromBase($symfonyRequest);
                $laravelResponse = $this->kernel->handle($laravelRequest);
                $this->kernel->terminate($laravelRequest, $laravelResponse);

                $psr7Response = $this->psrHttpFactory->createResponse($laravelResponse);

                $this->psr7Worker->respond($psr7Response);
            } finally {
                unset($psr7Request, $psr7Response, $symfonyRequest, $laravelRequest, $laravelResponse);
            }
        }
    }
}
