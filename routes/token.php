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

use Branca\Branca;

$app->post("/token", function ($request, $response, $arguments) {
    $requested_scopes = $request->getParsedBody() ?: [];

    $valid_scopes = [
        "todo.create",
        "todo.read",
        "todo.update",
        "todo.delete",
        "todo.list",
        "todo.all"
    ];

    $scopes = array_filter($requested_scopes, function ($needle) use ($valid_scopes) {
        return in_array($needle, $valid_scopes);
    });

    $now = new DateTime();
    $server = $request->getServerParams();

    $payload = [
        "sub" => $server["PHP_AUTH_USER"],
        "scope" => $scopes
    ];

    $branca = new Branca(getenv("BRANCA_SECRET"));
    $token = $branca->encode(json_encode($payload));

    $data["token"] = $token;

    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

/* This is just for debugging, not usefull in real life. */
$app->get("/dump", function ($request, $response, $arguments) {
    print_r($this->token);
});

$app->post("/dump", function ($request, $response, $arguments) {
    print_r($this->token);
});

/* This is just for debugging, not usefull in real life. */
$app->get("/info", function ($request, $response, $arguments) {
    phpinfo();
});
