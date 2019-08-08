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

use Skeleton\Domain\Token;
use Crell\ApiProblem\ApiProblem;
use Gofabian\Negotiation\NegotiationMiddleware;
use Micheh\Cache\CacheUtil;
use Tuupola\Middleware\JwtAuthentication;
use Tuupola\Middleware\HttpBasicAuthentication;
use Tuupola\Middleware\CorsMiddleware;
use Skeleton\Application\Response\UnauthorizedResponseFactory;

$container = $app->getContainer();

$container->set("HttpBasicAuthentication", function ($container) {
    return new HttpBasicAuthentication([
        "path" => "/token",
        "relaxed" => ["192.168.50.52", "127.0.0.1", "localhost"],
        "error" => function ($response, $arguments) {
            return (new UnauthorizedResponseFactory)->create(
                $arguments["message"],
                401
            );
        },
        "users" => [
            "test" => "test"
        ]
    ]);
});

$container->set("token", function ($container) {
    return new Token([]);
});

$container->set("JwtAuthentication", function ($container) {
    return new JwtAuthentication([
        "path" => "/",
        "ignore" => ["/token", "/info"],
        "secret" => getenv("JWT_SECRET"),
        "logger" => $container->get("logger"),
        "attribute" => false,
        "relaxed" => ["192.168.50.52", "127.0.0.1", "localhost"],
        "error" => function ($response, $arguments) {
            return (new UnauthorizedResponseFactory)->create(
                $arguments["message"],
                401
            );
        },
        "before" => function ($request, $arguments) use ($container) {
            $container->get("token")->populate($arguments["decoded"]);
        }
    ]);
});

$container->set("CorsMiddleware", function ($container) {
    return new CorsMiddleware([
        "logger" => $container->get("logger"),
        "origin" => ["*"],
        "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE"],
        "headers.allow" => ["Authorization", "If-Match", "If-Unmodified-Since"],
        "headers.expose" => ["Authorization", "Etag"],
        "credentials" => true,
        "cache" => 60,
        "error" => function ($request, $response, $arguments) {
            return (new UnauthorizedResponseFactory)->create(
                $arguments["message"],
                401
            );
        }
    ]);
});

$container->set("NegotiationMiddleware", function ($container) {
    return new NegotiationMiddleware([
        "accept" => ["application/json"]
    ]);
});

$app->add("HttpBasicAuthentication");
$app->add("JwtAuthentication");
$app->add("CorsMiddleware");
//$app->add("NegotiationMiddleware");

$container->set("cache", function ($container) {
    return new CacheUtil;
});
