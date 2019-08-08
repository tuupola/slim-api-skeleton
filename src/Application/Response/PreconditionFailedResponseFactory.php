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

namespace Skeleton\Application\Response;

use Crell\ApiProblem\ApiProblem;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Tuupola\Http\Factory\ResponseFactory;

class PreconditionFailedResponseFactory
{
    private $factory;

    public function __construct(ResponseFactoryInterface $factory = null)
    {
        $this->factory = $factory ?: new ResponseFactory;
    }

    public function create($message, $status = 412)
    {
        $problem = new ApiProblem($message, "about:blank");
        $problem->setStatus($status);

        $response = $this->factory->createResponse($status);
        $body = $response->getBody();
        $body->write($problem->asJson(true));
        return $response
            ->withHeader("Content-type", "application/problem+json")
            ->withBody($body);
    }
}
