<?php
declare(strict_types=1);

/*
 * This file is part of the Slim API skeleton package
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

namespace Skeleton\Infrastructure\Slim\Handler;

use Crell\ApiProblem\ApiProblem;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler;
use Exception;
use Throwable;

class ApiErrorHandler extends ErrorHandler
{
    protected function respond(): ResponseInterface
    {
        //$this->logger->critical($throwable->getMessage());

        $response = $this->responseFactory->createResponse();

        $status = $this->exception->getCode() ?: 500;
        $problem = new ApiProblem($this->exception->getMessage(), "about:blank");
        $problem->setStatus($status);

        if ($this->exception instanceof HttpMethodNotAllowedException) {
            $allowed = $this->exception->getAllowedMethods();

            /* 405 response must include Allow header. */
            $response = $response->withHeader("Allow", implode(", ", $allowed));

            /* Show allowed methods also in API problem body. */
            if (1 === count($allowed)) {
                $detail = "Request method must be {$allowed[0]}";
            } else {
                $last = array_pop($allowed);
                $first = implode(", ", $allowed);
                $detail = "Request method must be either {$first} or {$last}.";
            }
            $problem->setDetail($detail);
        }
        $json = $problem->asJson(true);

        $body = $response->getBody();
        $body->write($json);

        return $response
            ->withStatus($status)
            ->withHeader("Content-type", "application/problem+json")
            ->withBody($body);
    }
}
