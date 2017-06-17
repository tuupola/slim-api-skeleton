<?php

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

namespace Slim\Handlers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Crell\ApiProblem\ApiProblem;

final class NotFound extends \Slim\Handlers\Error
{
    public function __invoke(Request $request, Response $response)
    {
        $problem = new ApiProblem(
            "Not found",
            "http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html"
        );
        $problem->setStatus(404);
        $body = $problem->asJson(true);

        return $response
                ->withStatus(404)
                ->withHeader("Content-type", "application/problem+json")
                ->write($body);
    }
}
