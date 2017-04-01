<?php

namespace Slim\Handlers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Crell\ApiProblem\ApiProblem;

final class NotAllowed extends \Slim\Handlers\Error
{
    public function __invoke(Request $request, Response $response, $allowed = null)
    {
        $problem = new ApiProblem(
            "Method not allowed",
            "http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html"
        );
        $problem->setStatus(405);

        if ($allowed) {
            if (1 === count($allowed)) {
                $detail = "Request method must be {$allowed[0]}";
            } else {
                $last = array_pop($allowed);
                $first = implode(", ", $allowed);
                $detail = "Request method must be either {$first} or {$last}.";
            }
            $problem->setDetail($detail);
        }

        $body = $problem->asJson(true);

        return $response
                ->withStatus(405)
                ->withHeader("Content-type", "application/problem+json")
                ->write($body);
    }
}
