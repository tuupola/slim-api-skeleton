<?php

namespace Slim\Handlers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Crell\ApiProblem\ApiProblem;

final class ApiError extends \Slim\Handlers\Error
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(Request $request, Response $response, \Exception $exception)
    {
        $this->logger->critical($exception->getMessage());

        $status = $exception->getCode() ?: 500;

        $problem = new ApiProblem($exception->getMessage(), "about:blank");
        $problem->setStatus($status);
        $body = $problem->asJson(true);

        return $response
                ->withStatus($status)
                ->withHeader("Content-type", "application/problem+json")
                ->write($body);
    }
}
