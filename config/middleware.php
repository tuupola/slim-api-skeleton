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
use Micheh\Cache\CacheUtil;

$app->add(function ($request, $response, $next) {
    $response = $response
        ->withHeader("Access-Control-Allow-Headers", "Content-Type")
        ->withHeader("Access-Control-Allow-Methods", "GET,POST,PATCH,DELETE")
        ->withHeader("Access-Control-Allow-Origin", "*");
    return $next($request, $response);
});

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

$app->add("HttpBasicAuthentication");
$app->add("JwtAuthentication");

$container["cache"] = function ($container) {
    return new CacheUtil;
};

