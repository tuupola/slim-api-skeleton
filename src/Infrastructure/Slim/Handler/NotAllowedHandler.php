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

// namespace Skeleton\Infrastructure\Slim\Handler;

// use Crell\ApiProblem\ApiProblem;
// use Psr\Http\Message\ServerRequestInterface as Request;
// use Psr\Http\Message\ResponseInterface as Response;
// use Slim\Handlers\AbstractHandler;

// final class NotAllowedHandler extends AbstractHandler
// {
//     public function __invoke(Request $request, Response $response, $allowed = null)
//     {
//         $problem = new ApiProblem(
//             "Method not allowed",
//             "http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html"
//         );
//         $problem->setStatus(405);

//         if ($allowed) {
//             if (1 === count($allowed)) {
//                 $detail = "Request method must be {$allowed[0]}";
//             } else {
//                 $last = array_pop($allowed);
//                 $first = implode(", ", $allowed);
//                 $detail = "Request method must be either {$first} or {$last}.";
//             }
//             $problem->setDetail($detail);
//         }

//         $body = $problem->asJson(true);

//         return $response
//                 ->withStatus(405)
//                 ->withHeader("Content-type", "application/problem+json")
//                 ->write($body);
//     }
// }
