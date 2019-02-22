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

use Crell\ApiProblem\ApiProblem;
use Gofabian\Negotiation\NegotiationMiddleware;
use Micheh\Cache\CacheUtil;
use Skeleton\Application\Response\UnauthorizedResponse;
use Skeleton\Domain\Token;
use Tuupola\Middleware\CorsMiddleware;
use Tuupola\Middleware\HttpBasicAuthentication;
use Tuupola\Middleware\BrancaAuthentication;
use Tuupola\Middleware\ServerTimingMiddleware;

$container = $app->getContainer();

$container["HttpBasicAuthentication"] = function ($container) {
    return new HttpBasicAuthentication([
        "path" => "/token",
        "relaxed" => ["192.168.50.52", "127.0.0.1", "localhost"],
        "error" => function ($response, $arguments) {
            return new UnauthorizedResponse($arguments["message"], 401);
        },
        "users" => [
            "test" => "test"
        ]
    ]);
};

$container["token"] = function ($container) {
    return new Token;
};

$container["BrancaAuthentication"] = function ($container) {
    return new BrancaAuthentication([
        "path" => "/",
        "ttl" => 7200,
        "ignore" => ["/token", "/info"],
        "secret" => getenv("BRANCA_SECRET"),
        "logger" => $container["logger"],
        "attribute" => false,
        "relaxed" => ["192.168.50.52", "127.0.0.1", "localhost"],
        "error" => function ($response, $arguments) {
            return new UnauthorizedResponse($arguments["message"], 401);
        },
        "before" => function ($request, $arguments) use ($container) {
            $container["token"]->populate(json_decode($arguments["decoded"], true));
        }
    ]);
};

$container["CorsMiddleware"] = function ($container) {
    return new CorsMiddleware([
        "logger" => $container["logger"],
        "origin" => ["*"],
        "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE"],
        "headers.allow" => ["Authorization", "If-Match", "If-Unmodified-Since"],
        "headers.expose" => ["Authorization", "Etag"],
        "credentials" => true,
        "cache" => 60,
        "error" => function ($request, $response, $arguments) {
            return new UnauthorizedResponse($arguments["message"], 401);
        }
    ]);
};

$container["NegotiationMiddleware"] = function ($container) {
    return new NegotiationMiddleware([
        "accept" => ["application/json"]
    ]);
};

$container["ServerTimingMiddleware"] = function ($container) {
    return new ServerTimingMiddleware;
};

$app->add("HttpBasicAuthentication");
$app->add("BrancaAuthentication");
$app->add("CorsMiddleware");
$app->add("NegotiationMiddleware");
$app->add("ServerTimingMiddleware");

$container["cache"] = function ($container) {
    return new CacheUtil;
};
