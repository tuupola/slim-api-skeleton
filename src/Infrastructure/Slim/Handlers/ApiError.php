<?php

/*
 * This file is part of the Slim API skeleton package.
 *
 * This handler is called in case of otherwise uncaught exceptions.
 * However due to way Slim works CORS headers are lost. It would be better
 * to handle exceptions gracefully by returning one of the predefined
 * error responses instead.
 *
 * Copyright (c) 2016-2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/slim-api-skeleton
 *
 */

namespace Skeleton\Infrastructure\Slim\Handlers;

use Crell\ApiProblem\ApiProblem;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Handlers\AbstractError;
use Throwable;

final class ApiError extends AbstractError
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(Request $request, Response $response, Throwable $throwable)
    {
        $this->logger->critical($throwable->getMessage());

        $status = $throwable->getCode() ?: 500;

        $problem = new ApiProblem($throwable->getMessage(), "about:blank");
        $problem->setStatus($status);
        $body = $problem->asJson(true);

        return $response
                ->withStatus($status)
                ->withHeader("Content-type", "application/problem+json")
                ->write($body);
    }
}
