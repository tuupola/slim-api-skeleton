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

//namespace Tuupola\Http\Response;
namespace Response;

use Crell\ApiProblem\ApiProblem;
use Slim\Http\Headers;
use Slim\Http\Response;
use Slim\Http\Stream;

class ForbiddenResponse extends Response
{
    public function __construct($message, $status = 403)
    {
        $problem = new ApiProblem($message, "about:blank");
        $problem->setStatus($status);

        $handle = fopen("php://temp", "wb+");
        $body = new Stream($handle);
        $body->write($problem->asJson(true));
        $headers = (new Headers)->set("Content-type", "application/problem+json");
        parent::__construct($status, $headers, $body);
    }
}
