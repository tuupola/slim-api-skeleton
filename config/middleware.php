<?php

/*
 * This file is part of the Slim API skeleton package
 *
 * Copyright (c) 2016 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/slim-api-skeleton
 *
 */
use App\Token;

use Slim\Middleware\JwtAuthentication;
use Slim\Middleware\JwtAuthentication\RequestPathRule;
use Slim\Middleware\HttpBasicAuthentication;
use Tuupola\Middleware\Cors;
#use Neomerx\Cors\Strategies\Settings as CorsSettings;

use Micheh\Cache\CacheUtil;

$container = $app->getContainer();

$container["HttpBasicAuthentication"] = function ($container) {
    return new HttpBasicAuthentication([
        "path" => "/token",
        "relaxed" => ["192.168.50.52"],
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
        "secret" => getenv("JWT_SECRET"),
        "logger" => $container["logger"],
        "relaxed" => ["192.168.50.52"],
        "rules" => [
            new RequestPathRule([
                "path" => "/",
                "passthrough" => ["/token"]
            ])
        ],
        "error" => function ($request, $response, $arguments) {
            $data["status"] = "error";
            $data["message"] = $arguments["message"];
            return $response
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        },
        "callback" => function ($request, $response, $arguments) use ($container) {
            $container["token"]->hydrate($arguments["decoded"]);
        }
    ]);
};

$container["Cors"] = function ($container) {

    return new Cors([
        "logger" => $container["logger"],

        "origin" => ["*"],
        "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE"],
        "headers.allow" => ["Authorization", "If-Match", "If-Unmodified-Since"],
        "headers.expose" => ["Authorization", "Etag"],
        "credentials" => true,
        "cache" => 60,
    ]);
};

$app->add("HttpBasicAuthentication");
$app->add("JwtAuthentication");
$app->add("Cors");

$container["cache"] = function ($container) {
    return new CacheUtil;
};
