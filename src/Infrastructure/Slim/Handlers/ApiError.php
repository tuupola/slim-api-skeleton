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
