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

use App\Token;
use Crell\ApiProblem\ApiProblem;
use Gofabian\Negotiation\NegotiationMiddleware;
use Micheh\Cache\CacheUtil;
use Tuupola\Middleware\JwtAuthentication;
use Tuupola\Middleware\HttpBasicAuthentication;
use Tuupola\Middleware\CorsMiddleware;
use Response\UnauthorizedResponse;

$container = $app->getContainer();

$container["HttpBasicAuthentication"] = function ($container) {
    return new HttpBasicAuthentication([
        "path" => "/token",
        "relaxed" => ["192.168.50.52", "127.0.0.1", "localhost"],
        "error" => function ($request, $response, $arguments) {
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

$container["JwtAuthentication"] = function ($container) {
    return new JwtAuthentication([
        "path" => "/",
        "ignore" => ["/token", "/info"],
        "secret" => getenv("JWT_SECRET"),
        "logger" => $container["logger"],
        "attribute" => false,
        "relaxed" => ["192.168.50.52", "127.0.0.1", "localhost"],
        "error" => function ($request, $response, $arguments) {
            return new UnauthorizedResponse($arguments["message"], 401);
        },
        "before" => function ($request, $response, $arguments) use ($container) {
            $container["token"]->hydrate($arguments["decoded"]);
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

$container["Negotiation"] = function ($container) {
    return new NegotiationMiddleware([
        "accept" => ["application/json"]
    ]);
};

$app->add("HttpBasicAuthentication");
$app->add("JwtAuthentication");
$app->add("CorsMiddleware");
$app->add("Negotiation");

$container["cache"] = function ($container) {
    return new CacheUtil;
};
